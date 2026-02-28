<?php

namespace App\Http\Controllers\B2CECOM;

use App\Contact;
use App\BusinessLocation;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\Log;

class B2cOrdersController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $contactUtil;

    protected $businessUtil;

    protected $transactionUtil;

    protected $productUtil;
    protected $moduleUtil;

    private $receipt_details;

    protected $dummyPaymentLine;
    protected $shipping_status_colors;

    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;

        $this->dummyPaymentLine = [
            'method' => '',
            'amount' => 0,
            'note' => '',
            'card_transaction_number' => '',
            'card_number' => '',
            'card_type' => '',
            'card_holder_name' => '',
            'card_month' => '',
            'card_year' => '',
            'card_security' => '',
            'cheque_number' => '',
            'bank_account_number' => '',
            'is_return' => 0,
            'transaction_no' => '',
        ];

        $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-navy',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
    }
    public function mySaleOrders(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            $locationId = $request->route('location_id');
            $brandName = $request->route('brand_name');
            
            // Build query for orders
            $query = Transaction::where('contact_id', $contact->id)
                ->where('business_id', $business_id)
                ->where('type', 'sales_order')
                ->where('status', '!=', 'void') // Hide void orders
                ->select('id','business_id','type','status','final_total','invoice_no','transaction_date','payment_status','location_id');
            
            // Add location constraint for B2C routes
            if ($locationId) {
                $query->where('location_id', $locationId);
            }
            
            // Add brand constraint for brand-specific routes
            if ($brandName && $request->has('current_brand')) {
                $brand = $request->get('current_brand');
                if ($brand) {
                    $query->where('brand_id', $brand->id);
                }
            }
            
            $orders = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json([
                'status' => true,
                'data' => 
                [
                    'data' => $orders->getCollection(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'total' => $orders->total(),
                    'from' => $orders->firstItem(),
                    'per_page' => $orders->perPage(),
                    'to' => $orders->lastItem(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Error fetching orders', 'error' => $th->getMessage() . ' at ' . $th->getFile()]);
        }
    }
    public function mySaleInvoices(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            $locationId = $request->route('location_id');
            $brandName = $request->route('brand_name');
            
            // Build query for invoices
            $query = Transaction::where('contact_id', $contact->id)
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', '!=', 'void') // Hide void orders
                ->select('id','business_id','type','status','final_total','invoice_no','transaction_date','payment_status','location_id');
            
            // Add location constraint for B2C routes
            if ($locationId) {
                $query->where('location_id', $locationId);
            }
            
            // Add brand constraint for brand-specific routes
            if ($brandName && $request->has('current_brand')) {
                $brand = $request->get('current_brand');
                if ($brand) {
                    $query->where('brand_id', $brand->id);
                }
            }
            
            $orders = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json([
                'status' => true,
                'data' => 
                [
                    'data' => $orders->getCollection(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'total' => $orders->total(),
                    'from' => $orders->firstItem(),
                    'per_page' => $orders->perPage(),
                    'to' => $orders->lastItem(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Error', 'error' => $th->getMessage() . ' at ' . $th->getFile()]);
        }
    }

    public function mySaleReturns(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            $sale_type = 'sales_order';
            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true);
            $sells = $sells->where('transactions.status', 'ordered');
            $orders = $sells->with('payment_lines')->paginate(15);
            return response()->json([
                'status' => true,
                'data' => $orders
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Error', 'error' => $th->getMessage() . ' at ' . $th->getFile()]);
        }
    }

    public function getOrderDetails(Request $request, $orderId)
    {
        try {
            $orderId = $request->route('orderId');
            $uuid = $request->query('uuid',false);
            $contact=null;
            $business_id=null;
            if (!$uuid) {
                $contact = Auth::guard('api')->user();
                if (!$contact) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not Able to find your identity, if have any query contact us.',
                    ]);
                }
                $business_id = $contact->business_id;
            }

            $locationId = $request->route('location_id');
            $brandName = $request->route('brand_name');
            
            // Build query for order details
            $query = Transaction::with([
                'payment_lines'=> function($query){
                    $query->select('id','amount', 'paid_on','payment_ref_no','transaction_no','method');
                },
                'sell_lines' => function($query){
                    $query->select('id','transaction_id','product_id','variation_id','ordered_quantity','verified_qty','picked_quantity','unit_price','unit_price_before_discount','unit_price_inc_tax','item_tax','line_discount_type','line_discount_amount','is_free');
                },
                'sell_lines.product' => function ($query) {
                    $query->select('id', 'name', 'slug','image'); 
                },
                'sell_lines.variations' => function ($query) {
                    $query->select('id','product_id','name','sub_sku', 'var_barcode_no'); 
                },
                 'sell_lines.variations.media' => function($query) {
                    $query->select('id', 'file_name', 'model_id');
                }
            ])
            ->when($uuid, function($query) use ($uuid){
                $query->where('unique_public_url', $uuid);
            })
            ->when(!$uuid, function($query) use ($contact){
                $query->where('contact_id', $contact->id)->where('business_id', $contact->business_id);
            })
              ->where('id', $orderId)
              ->select('id','business_id','type','status','final_total','invoice_no','transaction_date','payment_status','location_id','billing_first_name','billing_last_name','billing_company','billing_address1','billing_address2','billing_city','billing_state','billing_country','billing_zip','billing_phone','billing_email','shipping_first_name','shipping_last_name','shipping_company','shipping_address1','shipping_address2','shipping_city','shipping_state','shipping_country','shipping_zip','discount_type','discount_amount','shipping_charges');
            
            // Add location constraint for B2C routes
            if ($locationId) {
                $query->where('location_id', $locationId);
            }
            
            // Add brand constraint for brand-specific routes
            // if ($brandName && $request->has('current_brand')) {
            //     $brand = $request->get('current_brand');
            //     if ($brand) {
            //         $query->where('brand_id', $brand->id);
            //     }
            // }
            
            $orders = $query->orderBy('created_at', 'desc')->first();
            try {
                if($orders->payment_status ==''){
                    $orders->payment_status = 'due';
                }
                $orders['items_total_tax'] = isset($orders->sell_lines) ? $orders->sell_lines->sum(function($line) {
                    return $line->item_tax * $line->ordered_quantity;
                }) : 0;
            } catch (\Throwable $th) {
                
            }
            return response()->json([
                'status' => true,
                'data' => $orders,
                
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Error', 'error' => $th->getMessage() . ' at ' . $th->getFile()]);
        }
    }
    private function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null,
        $is_package_slip = false,
        $from_pos_screen = true,
        $invoice_layout_id = null,
        $is_delivery_note = false
    ) {
        $output = [
            'is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => [],
        ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        if ($from_pos_screen && $location_details->print_receipt_on_invoice != 1) {
            return $output;
        }
        //Check if printing of invoice is enabled or not.
        //If enabled, get print type.
        $output['is_enabled'] = true;

        $invoice_layout_id = !empty($invoice_layout_id) ? $invoice_layout_id : $location_details->invoice_layout_id;
        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $invoice_layout_id);

        //Check if printer setting is provided.
        $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;
        $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

        $currency_details = [
            'symbol' => $business_details->currency_symbol,
            'thousand_separator' => $business_details->thousand_separator,
            'decimal_separator' => $business_details->decimal_separator,
        ];


        $receipt_details->currency = $currency_details; // later fix $receipt_details['currency']

        if ($is_package_slip) {
            $output['html_content'] = view('sale_pos.receipts.packing_slip', compact('receipt_details'))->render();

            return $output;
        }

        if ($is_delivery_note) {
            $output['html_content'] = view('sale_pos.receipts.delivery_note', compact('receipt_details'))->render();

            return $output;
        }

        $output['print_title'] = $receipt_details->invoice_no;
        //If print type browser - return the content, printer - return printer config data, and invoice format config
        if ($receipt_printer_type == 'printer') {
            $output['print_type'] = 'printer';
            $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
            $output['data'] = $receipt_details;
        } else {
            $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';

            $output['html_content'] = view($layout, compact('receipt_details'))->render();
        }

        return $output;
    }
    public function printInvoice(Request $request, $orderId)
    {
        try {
            $orderId = $request->route('orderId');
            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];

            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            $locationId = $request->route('location_id');
            $brandName = $request->route('brand_name');

            // Build query for transaction
            $query = Transaction::where('id', $orderId)
                ->where('contact_id', $contact->id)
                ->where('business_id', $business_id)
                ->with(['location']);
            
            // Add location constraint for B2C routes
            if ($locationId) {
                $query->where('location_id', $locationId);
            }
            
            // Add brand constraint for brand-specific routes
            if ($brandName && $request->has('current_brand')) {
                $brand = $request->get('current_brand');
                if ($brand) {
                    $query->where('brand_id', $brand->id);
                }
            }
            
            $transaction = $query->first();

            if (empty($transaction)) {
                return $output;
            }

            $printer_type = 'browser';
            if (!empty(request()->input('check_location')) && request()->input('check_location') == true) {
                $printer_type = $transaction->location->receipt_printer_type;
            }

            $is_package_slip = !empty($request->input('package_slip')) ? true : false;
            $is_delivery_note = !empty($request->input('delivery_note')) ? true : false;

            $invoice_layout_id = $transaction->is_direct_sale ? $transaction->location->sale_invoice_layout_id : null;
            $receipt = $this->receiptContent($business_id, $transaction->location_id, $orderId, $printer_type, $is_package_slip, false, $invoice_layout_id, $is_delivery_note);

            if (!empty($receipt)) {
                $output = ['success' => 1, 'receipt' => $receipt];
            }
        } catch (\Exception $e) {
            Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
    
}
