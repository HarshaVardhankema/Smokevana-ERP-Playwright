<?php

namespace App\Http\Controllers\Staff;

use App\Contact;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use App\Media;
use App\Transaction;
use App\Utils\ContactUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\CustomerPriceRecall;
use App\TaxRate;
use App\Product;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\User;
use App\Events\SellCreatedOrModified;
use App\Lead;
use App\LocationTaxCharge;
use App\Ticket;
use App\TicketActivity;
use App\TransactionSellLine;

class CommissionAgentController extends Controller
{
    protected $contactUtil;
    protected $notificationUtil;
    protected $contactController;
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $businessUtil;

    public function __construct(
        ContactUtil $contactUtil,
        NotificationUtil $notificationUtil,
        ContactController $contactController,
        TransactionUtil $transactionUtil,
        ProductUtil $productUtil,
        ModuleUtil $moduleUtil,
        BusinessUtil $businessUtil
    ) {
        $this->contactUtil = $contactUtil;
        $this->notificationUtil = $notificationUtil;
        $this->contactController = $contactController;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    public function createCustomer(Request $request)
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
            'transaction_limit' => 'nullable|string|min:0',
            "credit_limit" => 'nullable|string|min:0',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'contact_id' => 'nullable|string|max:50',
            'email' => ['required', 'email', 'max:100', Rule::unique('contacts', 'email')->whereNull('deleted_at')],
            'dob' => 'nullable|date',
            'FEIN-License' => 'required|file|mimes:pdf,jpg,png,jpeg|max:10240',
            'Tobacco-License' => 'required|file|mimes:pdf,jpg,png,jpeg|max:10240',
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
            $input = $request->only('first_name', 'middle_name', 'last_name', 'prefix', 'mobile', 'email', 'password', 'supplier_business_name', 'address_line_1', 'address_line_2', 'city', 'state', 'zip_code', 'country', 'dob', 'contact_type', 'tax_number', 'pay_term_number', 'pay_term_type', 'landline', 'alternate_number', 'contact_id', 'transaction_limit', 'credit_limit');

            // Get business_id from location
            $location = $request->get('current_location');
            $businessId = $location ? $location->business_id : 1;

            $input['business_id'] = $businessId;
            $input['created_by'] = $request->get('current_user')->id; // Fixed created_by
            $input['customer_group_id'] = 1; // Fixed role
            $input['type'] = 'customer';
            $input['country'] = 'US';
            $input['isApproved'] = null;
            if ($request->get('current_user')->is_cmmsn_agnt == 1) {
                $input['is_createdby_commission_agent'] = 1;
            }
            $input['location_id'] = config('services.b2b.location_id');

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
            $input['is_auto_send_due_notification'] = 1;
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
                            'created_by' => $request->get('current_user')->id,
                        ];

                        // Attach the file to the contact
                        $model = Contact::findOrFail($customer->id);
                        $model_note = $model->documentsAndnote()->create($input);
                        Media::attachMediaToModel($model_note, $input['business_id'], $fileName, null, null, $customer->id);
                    }
                }
            }
            DB::commit();

            $contact = (object)[
                'email' => $request->email,
                'mobile' => $request->phone
            ];
            $user = (object)[
                'name' => $customer->first_name . $customer->last_name,
                'mobile' => $request->phone,
                'email' => $request->email,
            ];
            SendNotificationJob::dispatch(true, 1, 'registration_confirmation', $user, $contact);

            return response()->json(['status' => true, 'message' => 'Customer registered successfully', 'userID' => '1049AD_' . $customer->id], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage() . ' at ' . $th->getLine(),], 500);
        }
    }

    public function getCustomer(Request $request)
    {
        $created_by = $request->get('current_user')->id;
        $customer = Contact::where('created_by', $created_by)->where('isApproved', 1)->get();
        if (empty($customer)) {
            return response()->json(['status' => false, 'message' => 'no data found'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Customer fetched successfully', 'customer' => $customer], 200);
    }

    public function getCustomerById(Request $request)
    {
        $id = $request->id;
        $customer = Contact::where('id', $id)->first();
        if (empty($customer)) {
            return response()->json(['status' => false, 'message' => 'Customer not found'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Customer fetched successfully', 'customer' => $customer], 200);
    }

    public function getSellOrder(Request $request)
    {
        $created_by = $request->get('current_user')->id;
        $sellOrder = Transaction::where('created_by', $created_by)->where('type', 'sales_order')->get();
        if (empty($sellOrder)) {
            return response()->json(['status' => false, 'message' => 'no data found'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Sell order fetched successfully', 'sellOrder' => $sellOrder], 200);
    }

    public function getSellOrderById(Request $request)
    {
        $id = $request->id;
        $sellOrder = Transaction::where('id', $id)->where('type', 'sales_order')->first();
        if ($sellOrder) {
            $sellOrder->load('sell_lines');
        } else {
            return response()->json(['status' => false, 'message' => 'Sell order not found'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Sell order fetched successfully', 'sellOrder' => $sellOrder], 200);
    }

    public function getSellsInvoice(Request $request)
    {
        $created_by = $request->get('current_user')->id;
        $sellInvoice = Transaction::where('created_by', $created_by)->where('type', 'sell')->get();
        
        // Return 200 with empty array if no data (not 404)
        return response()->json([
            'success' => true,
            'message' => $sellInvoice->isEmpty() ? 'No invoices found' : 'Sell invoices fetched successfully',
            'data' => $sellInvoice,
            'total' => $sellInvoice->count()
        ], 200);
    }

    public function getSellsInvoiceById(Request $request)
    {
        $id = $request->id;
        $sellInvoice = Transaction::where('id', $id)->where('type', 'sell')->first();
        
        if (!$sellInvoice) {
            // Return 404 for specific resource not found
            return response()->json([
                'success' => false,
                'message' => 'Sell invoice not found'
            ], 404);
        }
        
        // Load relationships
        $sellInvoice->load('sell_lines');

        return response()->json([
            'success' => true,
            'message' => 'Sell invoice fetched successfully',
            'data' => $sellInvoice
        ], 200);
    }

    public function createSell(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'contact_id' => 'required|integer|exists:contacts,id',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|integer|exists:products,id',
                'products.*.variation_id' => 'required|integer|exists:variations,id',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_price' => 'required|numeric|min:0',
                'status' => 'required|string|in:draft,final,quotation,proforma,ordered',
                'discount_type' => 'nullable|string|in:fixed,percentage',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_rate_id' => 'nullable|integer|exists:tax_rates,id',
                'location_id' => 'required|integer|exists:business_locations,id',
                'additional_notes' => 'nullable|string|max:1000',
                'is_suspend' => 'nullable|boolean',
                'is_credit_sale' => 'nullable|boolean',
                'payment' => 'nullable|array',
                'payment.*.method' => 'required_with:payment|string',
                'payment.*.amount' => 'required_with:payment|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $created_by = $request->get('current_user')->id;
            $customer = Contact::where('id', $request->contact_id)->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }


        $request->merge([
            "hidden_price_group" => $customer->customer_group_id,
            "billing_first_name" => $customer->billing_first_name,
            "billing_last_name" => $customer->billing_last_name,
            "billing_company" => $customer->billing_company,
            "billing_address1" => $customer->billing_address1,
            "billing_address2" => $customer->billing_address2,
            "billing_city" => $customer->billing_city,
            "billing_state" => $customer->billing_state,
            "billing_zip" => $customer->billing_zip,
            "billing_country" => $customer->billing_country,
            "billing_details" => "",
            "billing_address" => $customer->billing_address,
            "shipping_first_name" => $customer->shipping_first_name,
            "shipping_last_name" => $customer->shipping_last_name,
            "shipping_company" => $customer->shipping_company,
            "shipping_address1" => $customer->shipping_address1,
            "shipping_address2" => $customer->shipping_address2,
            "shipping_city" => $customer->shipping_city,
            "shipping_state" => $customer->shipping_state,
            "shipping_zip" => $customer->shipping_zip,
            "shipping_country" => $customer->shipping_country,
            "shipping_details" => "",
            "shipping_address" => $customer->shipping_address,
            "is_save_and_print" => "0",
            "price_group" => $customer->customer_group_id,
            "default_price_group" => "",
            "invoice_no" => "",
            "sell_document" => [],
            "sell_price_tax" => "includes",
            "search_product" => "",
            "rp_redeemed" => "0",
            "rp_redeemed_amount" => "0",
            "rp_redeemed_modal" => "",
            "tax_rate_id" => "",
            "tax_calculation_amount" => " 0.00 ",
            "is_direct_sale" => "1",
            "shipping_status" => "",
            "delivered_to" => "",
            "delivery_person" => "",
            "shipping_documents" => [],
            "additional_expense_key_1" => "",
            "additional_expense_value_1" => "0",
            "additional_expense_key_2" => "",
            "additional_expense_value_2" => "0",
            "additional_expense_key_3" => "",
            "additional_expense_value_3" => "0",
            "additional_expense_key_4" => "",
            "additional_expense_value_4" => "0",
            "recur_interval" => "",
            "recur_interval_type" => "days",
            "recur_repetitions" => "",
            "subscription_repeat_on" => "",
            "user_id" => $created_by
        ]);


        $is_direct_sale = true;
        $input = $request->except('_token');
        $input['is_quotation'] = 0;
        //status is send as quotation from Add sales screen.
        if ($input['status'] == 'quotation') {
            $input['status'] = 'draft';
            $input['is_quotation'] = 1;
            $input['sub_status'] = 'quotation';
        } elseif ($input['status'] == 'proforma') {
            $input['status'] = 'draft';
            $input['sub_status'] = 'proforma';
        }

        $is_credit_limit_exeeded = $this->transactionUtil->isCustomerCreditLimitExeeded($input);
        if ($is_credit_limit_exeeded !== false) {
            $credit_limit_amount = $this->transactionUtil->num_f($is_credit_limit_exeeded, true);
            return response()->json([
                'success' => false,
                'message' => __('lang_v1.cutomer_credit_limit_exeeded', ['credit_limit' => $credit_limit_amount]),
                'data' => null
            ], 400);
        }
        $is_transaction_limit_exeeded = $this->transactionUtil->isCustomerTransactionLimitExeeded($input);
        if ($is_transaction_limit_exeeded !== false) {
            $transaction_limit_amount = $this->transactionUtil->num_f($is_transaction_limit_exeeded, true);
            return response()->json([
                'success' => false,
                'message' => __('Transaction Limit Exeeded', ['transaction_limit' => $transaction_limit_amount]),
                'data' => null
            ], 400);
        }

        if (!empty($input['products'])) {
            $business_id = 1;

            //Check if subscribed or not, then check for users quota
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription expired',
                    'data' => null
                ], 403);
            } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice quota exceeded',
                    'data' => null
                ], 403);
            }

            $user_id = $input['user_id'];

            $discount = [
                'discount_type' => $input['discount_type'],
                'discount_amount' => $input['discount_amount'],
            ];
            $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);

            // Use database transaction with proper locking to prevent race conditions
            DB::beginTransaction();

            // Lock the invoice_schemes table to prevent duplicate invoice numbers
            $invoice_scheme_id = !empty($input['invoice_scheme_id']) ? $input['invoice_scheme_id'] : null;
            if (empty($invoice_scheme_id)) {
                $scheme = DB::table('business_locations')
                    ->where('business_id', $business_id)
                    ->where('id', $input['location_id'])
                    ->first();
                $invoice_scheme_id = $scheme->invoice_scheme_id ?? null;
            }

            if ($invoice_scheme_id) {
                DB::table('invoice_schemes')
                    ->where('id', $invoice_scheme_id)
                    ->lockForUpdate()
                    ->first();
            }

            // Lock the transactions table to prevent race conditions
            DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $input['transaction_date'] = \Carbon::now()->format('Y-m-d H:i:s');

            if ($is_direct_sale) {
                $input['is_direct_sale'] = 1;
            }


            //Set commission agent
            $input['commission_agent'] = $user_id;

            if (isset($input['exchange_rate']) && $this->transactionUtil->num_uf($input['exchange_rate']) == 0) {
                $input['exchange_rate'] = 1;
            }

            //Customer group details
            $contact_id = $request->get('contact_id', null);
            $cg = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
            $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;

            //set selling price group id
            $price_group_id = $request->has('price_group') ? $request->input('price_group') : null;

            //If default price group for the location exists
            $price_group_id = $price_group_id == 0 && $request->has('default_price_group') ? $request->input('default_price_group') : $price_group_id;

            $input['is_suspend'] = isset($input['is_suspend']) && 1 == $input['is_suspend'] ? 1 : 0;
            if ($input['is_suspend']) {
                $input['sale_note'] = !empty($input['additional_notes']) ? $input['additional_notes'] : null;
            }

            //Generate reference number
            if (!empty($input['is_recurring'])) {
                //Update reference count
                $ref_count = $this->transactionUtil->setAndGetReferenceCount('subscription');
                $input['subscription_no'] = $this->transactionUtil->generateReferenceNumber('subscription', $ref_count);
            }

            if (!empty($request->input('invoice_scheme_id'))) {
                $input['invoice_scheme_id'] = $request->input('invoice_scheme_id');
            }

            //Types of service
            if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
                $input['types_of_service_id'] = $request->input('types_of_service_id');
                $price_group_id = !empty($request->input('types_of_service_price_group')) ? $request->input('types_of_service_price_group') : $price_group_id;
                $input['packing_charge'] = !empty($request->input('packing_charge')) ?
                    $this->transactionUtil->num_uf($request->input('packing_charge')) : 0;
                $input['packing_charge_type'] = $request->input('packing_charge_type');
                $input['service_custom_field_1'] = !empty($request->input('service_custom_field_1')) ?
                    $request->input('service_custom_field_1') : null;
                $input['service_custom_field_2'] = !empty($request->input('service_custom_field_2')) ?
                    $request->input('service_custom_field_2') : null;
                $input['service_custom_field_3'] = !empty($request->input('service_custom_field_3')) ?
                    $request->input('service_custom_field_3') : null;
                $input['service_custom_field_4'] = !empty($request->input('service_custom_field_4')) ?
                    $request->input('service_custom_field_4') : null;
                $input['service_custom_field_5'] = !empty($request->input('service_custom_field_5')) ?
                    $request->input('service_custom_field_5') : null;
                $input['service_custom_field_6'] = !empty($request->input('service_custom_field_6')) ?
                    $request->input('service_custom_field_6') : null;
            }

            if ($request->input('additional_expense_value_1') != '') {
                $input['additional_expense_key_1'] = $request->input('additional_expense_key_1');
                $input['additional_expense_value_1'] = $request->input('additional_expense_value_1');
            }

            if ($request->input('additional_expense_value_2') != '') {
                $input['additional_expense_key_2'] = $request->input('additional_expense_key_2');
                $input['additional_expense_value_2'] = $request->input('additional_expense_value_2');
            }

            if ($request->input('additional_expense_value_3') != '') {
                $input['additional_expense_key_3'] = $request->input('additional_expense_key_3');
                $input['additional_expense_value_3'] = $request->input('additional_expense_value_3');
            }

            if ($request->input('additional_expense_value_4') != '') {
                $input['additional_expense_key_4'] = $request->input('additional_expense_key_4');
                $input['additional_expense_value_4'] = $request->input('additional_expense_value_4');
            }

            $input['selling_price_group_id'] = $price_group_id;

            if ($this->transactionUtil->isModuleEnabled('tables')) {
                $input['res_table_id'] = request()->get('res_table_id');
            }
            if ($this->transactionUtil->isModuleEnabled('service_staff')) {
                $input['res_waiter_id'] = request()->get('res_waiter_id');
            }

            if ($this->transactionUtil->isModuleEnabled('kitchen')) {
                $input['is_kitchen_order'] = request()->get('is_kitchen_order');
            }

            //upload document
            $input['document'] = $this->transactionUtil->uploadFile($request, 'sell_document', 'documents');

            $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id);

            //Upload Shipping documents
            Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');

            $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id']);

            $change_return['amount'] = $input['change_return'] ?? 0;
            $change_return['is_return'] = 1;

            // $input['payment'][] = $change_return;

            // $is_credit_sale = isset($input['is_credit_sale']) && $input['is_credit_sale'] == 1 ? true : false;

            // if (!$transaction->is_suspend && !empty($input['payment']) && !$is_credit_sale) {
            //     $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment']);
            // }

            //Check for final and do some processing.

            //custom
            if (isset($input['type']) && $input['type'] == 'sales_order') {
                foreach ($input['products'] as $product) {
                    $decrease_qty = $this->productUtil
                        ->num_uf($product['quantity']);
                    if (!empty($product['base_unit_multiplier'])) {
                        $decrease_qty = $decrease_qty * $product['base_unit_multiplier'];
                    }

                    if ($product['enable_stock']) {
                        $this->productUtil->decreaseProductInStock(
                            $product['product_id'],
                            $product['variation_id'],
                            $input['location_id'],
                            $decrease_qty,
                            0,
                            false
                        );
                    }
                }
            }
            // custom end 
            if ($input['status'] == 'final') {
                if (!$is_direct_sale) {
                    //set service staff timer
                    foreach ($input['products'] as $product_line) {
                        if (!empty($product_line['res_service_staff_id'])) {
                            $product = Product::find($product_line['product_id']);

                            if (!empty($product->preparation_time_in_minutes)) {
                                $service_staff = User::find($product_line['res_service_staff_id']);
                                

                                $base_time = \Carbon::parse($transaction->transaction_date);

                                //if already assigned set base time as available_at
                                if (!empty($service_staff->available_at) && \Carbon::parse($service_staff->available_at)->gt(\Carbon::now())) {
                                    $base_time = \Carbon::parse($service_staff->available_at);
                                }

                                $total_minutes = $product->preparation_time_in_minutes * $this->transactionUtil->num_uf($product_line['quantity']);

                                $service_staff->available_at = $base_time->addMinutes($total_minutes);
                                $service_staff->save();
                            }
                        }
                    }
                }
                //update product stock
                foreach ($input['products'] as $product) {
                    $decrease_qty = $this->productUtil
                        ->num_uf($product['quantity']);
                    if (!empty($product['base_unit_multiplier'])) {
                        $decrease_qty = $decrease_qty * $product['base_unit_multiplier'];
                    }

                    if ($product['enable_stock']) {
                        $this->productUtil->decreaseProductQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $input['location_id'],
                            $decrease_qty,
                            0,
                            true
                        );
                    }

                    if ($product['product_type'] == 'combo') {
                        //Decrease quantity of combo as well.
                        $this->productUtil
                            ->decreaseProductQuantityCombo(
                                $product['combo'],
                                $input['location_id']
                            );
                    }
                }


                //Update payment status
                $payment_status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

                $transaction->payment_status = $payment_status;
                $business_details = $this->businessUtil->getDetails($business_id);

                if ($business_details->enable_rp == 1) {
                    $redeemed = !empty($input['rp_redeemed']) ? $input['rp_redeemed'] : 0;
                    $this->transactionUtil->updateCustomerRewardPoints($contact_id, $transaction->rp_earned, 0, $redeemed);
                }

                //Allocate the quantity from purchase and add mapping of
                //purchase & sell lines in
                //transaction_sell_lines_purchase_lines table
                
                $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

                $business = [
                    'id' => $business_id,
                    'accounting_method' => $business_details->accounting_method,
                    'location_id' => $input['location_id'],
                    'pos_settings' => $pos_settings,
                ];
                $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');

                // dd($transaction);

                //Auto send notification
                // $whatsapp_link = $this->notificationUtil->autoSendNotification($business_id, 'new_sale', $transaction, $transaction->contact);
                SendNotificationJob::dispatch(false, $business_id, 'new_sale', null, $transaction->contact, $transaction);
                // Schedule due notification if payment terms are set
                // $this->scheduleDueNotification($transaction, $business_id);
            }

            if (!empty($transaction->sales_order_ids)) {
                $this->transactionUtil->updateSalesOrderStatus($transaction->sales_order_ids);
            }

            $this->moduleUtil->getModuleData('after_sale_saved', ['transaction' => $transaction, 'input' => $input]);

            Media::uploadMedia($business_id, $transaction, $request, 'documents');

            $this->transactionUtil->activityLog($transaction, 'added');

            DB::commit();

            SellCreatedOrModified::dispatch($transaction);
            //send webhook to woo commerce connector from erp side 
            if ($transaction->type == 'sales_order') {
                //send webhook to woo commerce connector from erp side 
                // --- Pouse for staging ---
                // WooCommerceWebhookSaleOrder::dispatch($transaction->id);               
            }
            if ($request->input('is_save_and_print') == 1) {
                $url = $this->transactionUtil->getInvoiceUrl($transaction->id, $business_id);
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => $url . '?print_on_load=true',
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'redirect' => $url . '?print_on_load=true',
                        'message' => 'Sale created successfully'
                    ], 201);
                }
            }

            $msg = trans('sale.pos_sale_added');
            $receipt = '';
            $invoice_layout_id = $request->input('invoice_layout_id');
            $print_invoice = false;
            if (!$is_direct_sale) {
                if ($input['status'] == 'draft') {
                    $msg = trans('sale.draft_added');

                    if ($input['is_quotation'] == 1) {
                        $msg = trans('lang_v1.quotation_added');
                        $print_invoice = true;
                    }
                } elseif ($input['status'] == 'final') {
                    $print_invoice = true;
                }
            }

            if ($transaction->is_suspend == 1 && empty($pos_settings['print_on_suspend'])) {
                $print_invoice = false;
            }

            // Permission check removed for API compatibility
            // if (!auth()->check() || !auth()->user()->hasPermissionTo('print_invoice')) {
            //     $print_invoice = false;
            // }

            if ($print_invoice) {
                $receipt = $this->receiptContent($business_id, $input['location_id'], $transaction->id, null, false, true, $invoice_layout_id);
            }

            $response_data = [
                'transaction_id' => $transaction->id,
                'invoice_no' => $transaction->invoice_no,
                'status' => $transaction->status,
                'final_total' => $transaction->final_total,
                'total_before_tax' => $transaction->total_before_tax,
                'tax_amount' => $transaction->tax_amount,
                'discount_amount' => $transaction->discount_amount,
                'transaction_date' => $transaction->transaction_date,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'mobile' => $customer->mobile,
                ],
                'products' => $transaction->sell_lines->map(function($line) {
                    return [
                        'product_id' => $line->product_id,
                        'variation_id' => $line->variation_id,
                        'quantity' => $line->quantity,
                        'unit_price' => $line->unit_price,
                        'line_total' => $line->line_total,
                        'product_name' => $line->product->name ?? 'N/A',
                    ];
                }),
                'receipt' => $receipt,
            ];

            if (!empty($whatsapp_link)) {
                $response_data['whatsapp_link'] = $whatsapp_link;
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
                'data' => $response_data
            ], 201);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'No products provided',
                'data' => null
            ], 400);
        }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the sell order',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => null
            ], 500);
        }
    }
    public function getProducts()
    {
        if (request()->ajax()) {
            $search_term = request()->input('term', '');
            $location_id = request()->input('location_id', null);
            $check_qty = request()->input('check_qty', false);
            $price_group_id = request()->input('price_group', null);
            $business_id = 1;
            $not_for_selling = request()->get('not_for_selling', null);
            $price_group_id = request()->input('price_group', '');
            $product_types = request()->get('product_types', []);

            $search_fields = request()->get('search_fields', ['name', 'sku', 'var_barcode_no']);

            $is_metrix = filter_var(request()->get('is_metrix', false), FILTER_VALIDATE_BOOLEAN);
            if ($is_metrix) {
                $result = $this->productUtil->filtermetrix($business_id, $search_term, $location_id, $not_for_selling, $price_group_id, $product_types, $search_fields, $check_qty);
            } else {
                if (in_array('sku', $search_fields)) {
                    $search_fields[] = 'sub_sku';
                }
                $result = $this->productUtil->filterProduct($business_id, $search_term, $location_id, $not_for_selling, $price_group_id, $product_types, $search_fields, $check_qty);
            }

            return response()->json($result);
        }
    }
    public function getProductRow($variation_id, $location_id)
    {
        $output = [];

        try {
            $quantity = request()->get('quantity', 1);
            $output = $this->getSellLineRow($variation_id, $location_id, $quantity);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output['success'] = false;
            $output['msg'] = __('lang_v1.item_out_of_stock');
        }

        return response()->json($output);
    }
    private function getSellLineRow($variation_id, $location_id, $quantity, $so_line = null)
    {
        $business_id = 1;
        $business_details = $this->businessUtil->getDetails($business_id);
        //Check for weighing scale barcode

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $check_qty = !empty($pos_settings['allow_overselling']) ? false : true;

        $is_sales_order = request()->has('is_sales_order') && request()->input('is_sales_order') == 'true' ? true : false;
        $is_draft = request()->has('is_draft') && request()->input('is_draft') == 'true' ? true : false;

        if ($is_sales_order || !empty($so_line) || $is_draft) {
            $check_qty = false;
        }

        if (request()->input('disable_qty_alert') === 'true') {
            $pos_settings['allow_overselling'] = true;
        }

        $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id, $check_qty);

        if (!isset($product->quantity_ordered)) {
            $product->quantity_ordered = $quantity;
        }

        $product->secondary_unit_quantity = !isset($product->secondary_unit_quantity) ? 0 : $product->secondary_unit_quantity;
        $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit_id, false, $product->product_id);

        //Get customer group and change the price accordingly
        $customer_id = request()->get('customer_id', null);
        // erp custom change of recall price
        $recall_price = null;
        if ($customer_id) {
            $recall_price = CustomerPriceRecall::where('contact_id', $customer_id)
                ->where('product_id', $product->product_id)
                ->where('is_active', 1)
                ->where('is_deleted', 0)
                ->where('variation_id', $variation_id)->first();
            if ($recall_price) {
                $recall_price = $recall_price->new_price;
            }
        }
        
        $cg = $this->contactUtil->getCustomerGroup($business_id, $customer_id);
        
        // Get price from selling price group if customer group has one
        $price_group = null;
        if (!empty($cg) && !empty($cg->selling_price_group_id)) {
            $price_group = $cg->selling_price_group_id;
        } else {
            $price_group = request()->input('price_group');
        }
        
        // Apply selling price group price first
        if (!empty($price_group)) {
            $variation_group_prices = $this->productUtil->getVariationGroupPrice($variation_id, $price_group, $product->tax_id);

            if (!empty($variation_group_prices['price_inc_tax'])) {
                $product->sell_price_inc_tax = $variation_group_prices['price_inc_tax'];
                $product->default_sell_price = $variation_group_prices['price_exc_tax'];
            }
        }
        
        // Apply price_percentage from customer group to the selling price group price
        if (!empty($cg) && !empty($cg->price_percentage) && !empty($cg->selling_price_group_id)) {
            $percent = $cg->price_percentage;
            $product->default_sell_price = $product->default_sell_price + ($percent * $product->default_sell_price / 100);
            $product->sell_price_inc_tax = $product->sell_price_inc_tax + ($percent * $product->sell_price_inc_tax / 100);
        } else {
            // Fallback to old logic for backward compatibility
            $percent = (empty($cg) || empty($cg->amount) || $cg->price_calculation_type != 'percentage') ? 0 : $cg->amount;
            if ($percent > 0) {
                $product->default_sell_price = $product->default_sell_price + ($percent * $product->default_sell_price / 100);
                $product->sell_price_inc_tax = $product->sell_price_inc_tax + ($percent * $product->sell_price_inc_tax / 100);
            }
        }

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);

        $enabled_modules = $this->transactionUtil->allModulesEnabled();

        $purchase_line_id = request()->get('purchase_line_id');
        $output['success'] = true;
        $output['enable_sr_no'] = $product->enable_sr_no;

            $last_sell_line = $this->getLastSellLineForCustomer($variation_id, $customer_id, $location_id);

            $is_cg = !empty($cg->id) ? true : false;
            $discount = $this->productUtil->getProductDiscount($product, $business_id, $location_id, $is_cg, $price_group, $variation_id);


            $edit_discount = auth()->user()->can('edit_product_discount_from_sale_screen');
            $edit_price = auth()->user()->can('edit_product_price_from_sale_screen');


        // Add all data to output
        $output['product']= $product;
        $output['recall_price'] = $recall_price;
        $output['tax_dropdown'] = $tax_dropdown;
        $output['quantity'] = $quantity;
        $output['enabled_modules'] = $enabled_modules;
        $output['pos_settings'] = $pos_settings;
        $output['sub_units'] = $sub_units;
        $output['discount'] = $discount;
        $output['edit_discount'] = $edit_discount;
        $output['edit_price'] = $edit_price;
        $output['purchase_line_id'] = $purchase_line_id;
        $output['is_sales_order'] = $is_sales_order;
        $output['last_sell_line'] = $last_sell_line;
        $output['price_group'] = $price_group;

        return $output;
    }
    private function getLastSellLineForCustomer($variation_id, $customer_id, $location_id)
    {
        $sell_line = TransactionSellLine::join('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
            ->where('t.location_id', $location_id)
            ->where('t.contact_id', $customer_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->where('transaction_sell_lines.variation_id', $variation_id)
            ->orderBy('t.transaction_date', 'desc')
            ->select('transaction_sell_lines.*')
            ->first();

        return $sell_line;
    }
    public function listTaxRates(){
        $locationTaxTypes = LocationTaxCharge::join('location_tax_types', 'location_tax_charges.location_id', '=', 'location_tax_types.id')
        ->where('location_tax_charges.web_location_id', config('services.b2b.location_id'))
        ->select(
            'location_tax_charges.id',
            'location_tax_types.name as location_tax_type_name',
            'location_tax_types.id as location_tax_type_id',
            'location_tax_charges.state_code',
            'location_tax_charges.tax_type',
            'location_tax_charges.value'
        )
        ->get() // Use get() to retrieve the results
        ->toArray(); // Convert to array after getting the results

    return response()->json($locationTaxTypes);
    }

    public function storeLeads(Request $request)
    {
        $user_id = request()->get('current_user')->id;
        $leads = Lead::where('business_id', config('services.b2b.location_id'))
        ->where('status', 'pending')
        ->orWhere('visited_by', $user_id)
        ->get();
        return response()->json(['status' => true, 'message' => 'Leads fetched successfully', 'lead' => $leads], 200);
    }
    public function createLeads(Request $request)
    {
        // Validate incoming request
        $validator = \Validator::make($request->all(), [
            'store_name' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20'
        ], [
            'store_name.required' => 'Store name is required',
            'address_line_1.required' => 'Address line 1 is required',
            'city.required' => 'City is required',
            'state.required' => 'State is required',
            'country.required' => 'Country is required',
            'zip_code.required' => 'Zip code is required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = request()->get('current_user')->id;
        
        try {
            $input = $request->only(['store_name', 'address_line_1', 'address_line_2', 'state', 'city', 'country', 'zip_code']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $user_id;
            
            // Validate business_id exists
            if (empty($input['business_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business ID not found in session'
                ], 400);
            }
            
            // Always set status to pending
            $input['status'] = 'pending';
            
            // Generate reference number with LD prefix + 6 digits
            $lastLead = Lead::where('business_id', $input['business_id'])
                ->whereNotNull('reference_no')
                ->orderBy('id', 'desc')
                ->first();
            
            if ($lastLead && $lastLead->reference_no) {
                // Extract number from last reference (e.g., LD000001 -> 1)
                $lastNumber = intval(substr($lastLead->reference_no, 2));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            // Format as LD + 6 digits (e.g., LD000001)
            $input['reference_no'] = 'LD' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

            // Combine address components into full_address
            $addressComponents = array_filter([
                $input['address_line_1'] ?? '',
                $input['address_line_2'] ?? '',
                $input['city'] ?? '',
                $input['state'] ?? '',
                $input['country'] ?? '',
                $input['zip_code'] ?? ''
            ]);
            
            $input['full_address'] = implode(', ', $addressComponents);

            $lead = Lead::create($input);

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully',
                'data' => [
                    'lead' => $lead,
                    'reference_no' => $lead->reference_no
                ]
            ], 201);
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while processing your request'
            ], 500);
        }
    }
    public function getCustomerDue($contact_id)
    {
            $business_id = 1;
            $due = $this->transactionUtil->getContactDue($contact_id, $business_id);
            $contact = Contact::where('id', $contact_id)
                ->where("is_createdby_commission_agent", 1)
                ->first();
            if ($contact) {
                if ($contact['isApproved'] == 1) {
                    $output = [
                        'status' => true,
                        'message' => 'Customer found',
                        'due' => $due ? $due : 0,
                        'customer' => $contact,
                    ];
                } else {
                    $output = [
                        'status' => false,
                        'message' => 'Customer not approved',
                        'customer' => null,
                    ];
                }
            } else {
                $output = [
                    'status' => false,
                    'message' => 'Customer not found',
                    'customer' => null,
                ];
            }

            return $output;
        }
    public function storeLeadById($id)
    {
        $lead = Lead::findOrFail($id);
        return response()->json(['status' => true, 'message' => 'Lead fetched successfully', 'lead' => $lead], 200);
    }


    public function getTickets($lead_id)
    {
        try {
        $user_id = request()->get('current_user')->id;
        $tickets = Ticket::where('user_id', $user_id)->where('lead_id', $lead_id)->get();
        if(!$tickets){
            return response()->json(['status' => false, 'message' => 'Tickets not found'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Tickets fetched successfully', 'tickets' => $tickets], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Tickets not found'], 404);
        }
    }
    public function createTicket($lead_id)
    {
        try {
        $user_id = request()->get('current_user')->id;
        $ticket_description = request()->ticket_description;
        $issue_type = request()->issue_type;
        $issue_priority = request()->issue_priority;

        // Validate issue_type only if column exists
        if (\Schema::hasColumn('tickets', 'issue_type')) {
            $validIssueTypes = ['technical', 'billing', 'product', 'service', 'complaint', 'inquiry', 'other'];
            if ($issue_type && !in_array($issue_type, $validIssueTypes)) {
                return response()->json(['status' => false, 'message' => 'Invalid issue type'], 400);
            }
        }

        // Validate issue_priority only if column exists
        if (\Schema::hasColumn('tickets', 'issue_priority')) {
            $validIssuePriorities = ['low', 'medium', 'high', 'urgent'];
            if ($issue_priority && !in_array($issue_priority, $validIssuePriorities)) {
                return response()->json(['status' => false, 'message' => 'Invalid issue priority. Valid values: low, medium, high, urgent'], 400);
            }

            // Default priority to 'medium' if not provided
            if (!$issue_priority) {
                $issue_priority = 'medium';
            }
        }

        // Handle image upload only if column exists
        $initialImage = null;
        if (\Schema::hasColumn('tickets', 'initial_image') && request()->hasFile('image')) {
            $file = request()->file('image');
            
            // Validate image file
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Invalid image format. Allowed: jpg, jpeg, png, gif, webp'
                ], 400);
            }

            // Check file size (max 10MB)
            if ($file->getSize() > 10485760) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Image size too large. Maximum 10MB allowed.'
                ], 400);
            }

            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Create directory if it doesn't exist
            $uploadPath = public_path('uploads/tickets');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $fileName);
            $initialImage = $fileName;
        }

        // Generate reference number only if column exists
        $reference_no = null;
        if (\Schema::hasColumn('tickets', 'reference_no')) {
            $lastTicket = Ticket::whereNotNull('reference_no')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastTicket && $lastTicket->reference_no) {
                $lastNumber = intval(substr($lastTicket->reference_no, 2));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $reference_no = 'TI' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        }
        
        $lead = Lead::findOrFail($lead_id);
        if(!$lead){
            return response()->json(['status' => false, 'message' => 'Lead not found'], 404);
        }
        $lead->visited_by = $user_id;
        $lead->status = 'visited';
        $lead->save();
        
        // Build ticket data based on available columns
        $ticketData = [
            'lead_id' => $lead_id,
            'user_id' => $user_id,
            'ticket_description' => $ticket_description,
            'status' => 'open',
        ];
        
        // Add optional columns only if they exist
        if (\Schema::hasColumn('tickets', 'issue_type') && $issue_type) {
            $ticketData['issue_type'] = $issue_type;
        }
        
        if (\Schema::hasColumn('tickets', 'issue_priority') && $issue_priority) {
            $ticketData['issue_priority'] = $issue_priority;
        }
        
        if (\Schema::hasColumn('tickets', 'initial_image') && $initialImage) {
            $ticketData['initial_image'] = $initialImage;
        }
        
        if (\Schema::hasColumn('tickets', 'reference_no') && $reference_no) {
            $ticketData['reference_no'] = $reference_no;
        }
        
        $ticket = Ticket::create($ticketData);
        
        // Create ticket activity
        $ticketActivity = TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user_id,
            'activity_type' => 'created',
            'activity_details' => 'Ticket created'
        ]);

        // If image was uploaded, create an image activity too
        if ($initialImage) {
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user_id,
                'activity_type' => 'image',
                'activity_details' => 'Initial issue image attached',
                'attachment' => $initialImage
            ]);
        }

        // Add image URL to response
        $ticketResponse = $ticket->toArray();
        $ticketResponse['initial_image_url'] = $initialImage ? url('uploads/tickets/' . $initialImage) : null;

        return response()->json([
            'status' => true, 
            'message' => 'Ticket created successfully', 
            'ticket' => $ticketResponse
        ], 200);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Ticket not created', 'error' => $e->getMessage()], 500);
        }
    }
    public function getTicketById($id)
    {
        try {
        $user_id = request()->get('current_user')->id;
        $ticket = Ticket::with(['lead', 'user', 'closedBy'])->findOrFail($id);
        if(!$ticket){
            return response()->json(['status' => false, 'message' => 'Ticket not found'], 404);
        }
        if($ticket->user_id != $user_id){
            return response()->json(['status' => false, 'message' => 'You are not authorized to view this ticket'], 403);
            }
            return response()->json(['status' => true, 'message' => 'Ticket fetched successfully', 'ticket' => $ticket], 200);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Ticket not found'], 404);
        }
    }

    public function getTicketActivities($id)
    {
        try {
        $user_id = request()->get('current_user')->id;
        $ticket = Ticket::findOrFail($id);
        if(!$ticket){
            return response()->json(['status' => false, 'message' => 'Ticket not found'], 404);
        }
        if($ticket->user_id != $user_id){
            return response()->json(['status' => false, 'message' => 'You are not authorized to view this ticket'], 403);
        }
        $activities = $ticket->activities()->with('user')->orderBy('created_at', 'asc')->get();
        
        // Format activities for API response
        $formattedActivities = $activities->map(function($activity) {
            return [
                'id' => $activity->id,
                'ticket_id' => $activity->ticket_id,
                'user_id' => $activity->user_id,
                'activity_type' => $activity->activity_type,
                'activity_details' => $activity->activity_details,
                'attachment' => $activity->attachment,
                'file_url' => $activity->file_url,
                'created_at' => $activity->created_at->toISOString(),
                'user' => [
                    'id' => $activity->user->id,
                    'name' => $activity->user->first_name . ' ' . $activity->user->last_name,
                    'first_name' => $activity->user->first_name,
                    'last_name' => $activity->user->last_name,
                ],
                'is_image' => $activity->isImage(),
                'file_extension' => $activity->getFileExtension(),
            ];
        });
        
        return response()->json(['status' => true, 'message' => 'Ticket activities fetched successfully', 'activities' => $formattedActivities], 200);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Ticket not found'], 404);
        }
    }
    public function addMessageToTicket($id)
    {
        try {
            $user_id = request()->get('current_user')->id;
            $ticket = Ticket::findOrFail($id);
            
            if (!$ticket) {
                return response()->json(['status' => false, 'message' => 'Ticket not found'], 404);
            }
            
            if ($ticket->user_id != $user_id) {
                return response()->json(['status' => false, 'message' => 'You are not authorized to add message to this ticket'], 404);
            }

            $message = request()->input('message');
            $attachment = null;
            $activityType = 'text'; // Default type

            // Handle file upload
            if (request()->hasFile('attachment')) {
                $file = request()->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Create directory if it doesn't exist
                $uploadPath = public_path('uploads/tickets');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $file->move($uploadPath, $fileName);
                $attachment = $fileName;

                // Determine activity type based on file extension
                $extension = strtolower($file->getClientOriginalExtension());
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $activityType = 'image';
                } else {
                    $activityType = 'file';
                }

                // If no message provided for file upload, use filename
                if (empty($message)) {
                    $message = 'Sent a file: ' . $file->getClientOriginalName();
                }
            }

            // Validate: either message or attachment must be present
            if (empty($message) && empty($attachment)) {
                return response()->json(['status' => false, 'message' => 'Please enter a message or upload a file'], 400);
            }

            // Create activity
            $activity = $ticket->activities()->create([
                'activity_details' => $message,
                'activity_type' => $activityType,
                'attachment' => $attachment,
                'user_id' => $user_id,
            ]);

            // Load user relationship
            $activity->load('user');

            // Broadcast the message via WebSocket to all users viewing this ticket
            // Note: Not using ->toOthers() because we want ALL web viewers to receive it,
            // even if the API user and web viewer are the same person
            try {
                \Log::info('Broadcasting TicketMessageEvent for ticket #' . $ticket->id . ' and activity #' . $activity->id);
                broadcast(new \App\Events\TicketMessageEvent($activity));
                \Log::info('TicketMessageEvent broadcast successful');
            } catch (\Exception $broadcastException) {
                \Log::error('Broadcast failed: ' . $broadcastException->getMessage());
            }

            // Prepare activity data for response
            $activityData = [
                'id' => $activity->id,
                'ticket_id' => $activity->ticket_id,
                'user_id' => $activity->user_id,
                'activity_type' => $activity->activity_type,
                'activity_details' => $activity->activity_details,
                'attachment' => $activity->attachment,
                'file_url' => $activity->file_url,
                'created_at' => $activity->created_at->toISOString(),
                'user' => [
                    'id' => $activity->user->id,
                    'name' => $activity->user->first_name . ' ' . $activity->user->last_name,
                    'first_name' => $activity->user->first_name,
                    'last_name' => $activity->user->last_name,
                ],
                'is_image' => $activity->isImage(),
                'file_extension' => $activity->getFileExtension(),
            ];
            
            return response()->json([
                'status' => true, 
                'message' => 'Message added to ticket successfully',
                'activity' => $activityData
            ], 200);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Update ticket status (Admin can close, Sales rep cannot)
     */
    public function updateTicketStatus($id)
    {
        try {
            $user = request()->get('current_user');
            $ticket = Ticket::findOrFail($id);
            
            if (!$ticket) {
                return response()->json(['status' => false, 'message' => 'Ticket not found'], 404);
            }
            
            // Check if user is assigned to this ticket or is admin
            $isAdmin = $user->hasRole('Admin#' . $ticket->lead->business_id);
            if ($ticket->user_id != $user->id && !$isAdmin) {
                return response()->json(['status' => false, 'message' => 'You are not authorized to update this ticket'], 403);
            }
            
            $newStatus = request()->input('status');
            $validStatuses = ['open', 'in_progress', 'pending', 'resolved', 'closed'];
            
            if (!in_array($newStatus, $validStatuses)) {
                return response()->json(['status' => false, 'message' => 'Invalid status'], 400);
            }
            
            // Only admin can close tickets
            if ($newStatus === 'closed' && !$isAdmin) {
                return response()->json(['status' => false, 'message' => 'Only administrators can close tickets'], 403);
            }
            
            // Track status change
            if ($ticket->status != $newStatus) {
                $updateData = ['status' => $newStatus];
                
                // If closing the ticket, record who closed it and when
                if ($newStatus === 'closed') {
                    $updateData['closed_by'] = $user->id;
                    $updateData['closed_at'] = now();
                }
                
                $activity = TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'activity_type' => 'status_changed',
                    'activity_details' => 'Status changed from ' . ucfirst(str_replace('_', ' ', $ticket->status)) . ' to ' . ucfirst(str_replace('_', ' ', $newStatus))
                ]);
                
                $ticket->update($updateData);
                
                // Broadcast the status change via WebSocket
                try {
                    broadcast(new \App\Events\TicketMessageEvent($activity->load('user')));
                } catch (\Exception $broadcastException) {
                    \Log::error('Broadcast failed: ' . $broadcastException->getMessage());
                }
            }
            
            return response()->json([
                'status' => true, 
                'message' => 'Status updated successfully',
                'ticket' => $ticket->fresh()
            ], 200);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Get all tickets for admin (across all sales reps)
     */
    public function getAllTickets()
    {
        try {
            $user = request()->get('current_user');
            $business_id = $user->business_id;
            
            // Check if user is admin
            if (!$user->hasRole('Admin#' . $business_id)) {
                return response()->json(['status' => false, 'message' => 'Unauthorized. Admin access required.'], 403);
            }
            
            $tickets = Ticket::whereHas('lead', function ($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })
            ->with(['lead', 'user', 'closedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
            
            return response()->json([
                'status' => true, 
                'message' => 'Tickets fetched successfully', 
                'tickets' => $tickets
            ], 200);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Close a ticket (Admin only)
     */
    public function closeTicket($id)
    {
        try {
            $user = request()->get('current_user');
            $ticket = Ticket::findOrFail($id);
            
            if (!$ticket) {
                return response()->json(['status' => false, 'message' => 'Ticket not found'], 404);
            }
            
            // Check if user is admin
            $isAdmin = $user->hasRole('Admin#' . $ticket->lead->business_id);
            if (!$isAdmin) {
                return response()->json(['status' => false, 'message' => 'Only administrators can close tickets'], 403);
            }
            
            // Close the ticket
            if ($ticket->status !== 'closed') {
                $ticket->update([
                    'status' => 'closed',
                    'closed_by' => $user->id,
                    'closed_at' => now()
                ]);
                
                // Create activity log
                $activity = TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'activity_type' => 'status_changed',
                    'activity_details' => 'Ticket closed by admin'
                ]);
                
                // Broadcast the status change
                try {
                    broadcast(new \App\Events\TicketMessageEvent($activity->load('user')));
                } catch (\Exception $broadcastException) {
                    \Log::error('Broadcast failed: ' . $broadcastException->getMessage());
                }
            }
            
            return response()->json([
                'status' => true, 
                'message' => 'Ticket closed successfully',
                'ticket' => $ticket->fresh(['lead', 'user', 'closedBy'])
            ], 200);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);
        }
    }

}
