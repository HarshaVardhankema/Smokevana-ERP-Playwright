<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\Product;
use App\PurchaseLine;
use App\TaxRate;
use App\Transaction;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use App\Events\PurchaseCreatedOrModified;
use App\TransactionPayment;

class PurchaseController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('purchase.view') && ! auth()->user()->can('purchase.create') && ! auth()->user()->can('view_own_purchase')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchases = $this->transactionUtil->getListPurchases($business_id);

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->supplier_id)) {
                $purchases->where('contacts.id', request()->supplier_id);
            }
            if (! empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }
            if (! empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $purchases->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $purchases->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            if (! empty(request()->status)) {
                $purchases->where('transactions.status', request()->status);
            }

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (! auth()->user()->can('purchase.view') && auth()->user()->can('view_own_purchase')) {
                $purchases->where('transactions.created_by', request()->session()->get('user.id'));
            }

            return Datatables::of($purchases)
                ->addColumn('action', function ($row) {
                    if($row->status == 'void'){
                        return '';
                    }
                    $html = '<div class="btn-group dropdown scroll-safe-dropdown">
                            <button type="button" class="btn-modal tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max dropdown-toggle" 
                                data-toggle="dropdown" aria-expanded="false">'.
                                __('messages.actions').
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    if (auth()->user()->can('purchase.view')) {
                        $html .= '<li><a href="#" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>'.__('messages.view').'</a></li>';
                    }
                    if (auth()->user()->can('purchase.view')) {
                        $html .= '<li><a href="#" class="print-invoice" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'printInvoice'], [$row->id]).'"><i class="fas fa-print" aria-hidden="true"></i>'.__('messages.print').'</a></li>';
                    }
                    if (auth()->user()->can('purchase.update')) {
                        // $html .= '<li><a href="'.action([\App\Http\Controllers\PurchaseController::class, 'edit'], [$row->id]).'"><i class="fas fa-edit"></i>'.__('messages.edit').'</a></li>';
                    }
                    if (auth()->user()->can('purchase.delete')) {
                        if($row->status != 'void'){
                            $html .= '<li><a href="'.action([\App\Http\Controllers\PurchaseController::class, 'voidPurchase'], [$row->id]).'" class="delete-purchase"><i class="fas fa-trash"></i>Void </a></li>';
                        }
                        // $html .= '<li><a href="'.action([\App\Http\Controllers\PurchaseController::class, 'destroy'], [$row->id]).'" class="delete-purchase"><i class="fas fa-trash"></i>'.__('messages.delete').'</a></li>';
                    }

                    $html .= '<li><a href="'.action([\App\Http\Controllers\LabelsController::class, 'show']).'?purchase_id='.$row->id.'" data-toggle="tooltip" title="'.__('lang_v1.label_help').'"><i class="fas fa-barcode"></i>'.__('barcode.labels').'</a></li>';

                    if (auth()->user()->can('purchase.view') && ! empty($row->document)) {
                        $document_name = ! empty(explode('_', $row->document, 2)[1]) ? explode('_', $row->document, 2)[1] : $row->document;
                        $html .= '<li><a href="'.url('uploads/documents/'.$row->document).'" download="'.$document_name.'"><i class="fas fa-download" aria-hidden="true"></i>'.__('purchase.download_document').'</a></li>';
                        if (isFileImage($document_name)) {
                            $html .= '<li><a href="#" data-href="'.url('uploads/documents/'.$row->document).'" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>'.__('lang_v1.view_document').'</a></li>';
                        }
                    }

                    if (auth()->user()->can('purchase.payments') ||
                        auth()->user()->can('edit_purchase_payment') ||
                        auth()->user()->can('delete_purchase_payment')) {
                        $html .= '<li class="divider"></li>';
                        if ($row->payment_status != 'paid') {
                            $html .= '<li><a href="'.action([\App\Http\Controllers\TransactionPaymentController::class, 'addPayment'], [$row->id]).'" class="add_payment_modal"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>'.__('purchase.add_payment').'</a></li>';
                        }

                        $html .= '<li><a href="'.action([\App\Http\Controllers\TransactionPaymentController::class, 'show'], [$row->id]).
                        '" class="view_payment_modal"><i class="fas fa-money-bill-alt" aria-hidden="true" ></i>'.__('purchase.view_payments').'</a></li>';
                    }

                    if (auth()->user()->can('purchase.update')) {
                        $html .= '<li><a href="'.action([\App\Http\Controllers\PurchaseReturnController::class, 'add'], [$row->id]).
                        '"><i class="fas fa-undo" aria-hidden="true" ></i>'.__('lang_v1.purchase_return').'</a></li>';
                    }

                    if (auth()->user()->can('purchase.update') || auth()->user()->can('purchase.update_status')) {
                        $html .= '<li><a href="#" data-purchase_id="'.$row->id.
                        '" data-status="'.$row->status.'" class="update_status"><i class="fas fa-edit" aria-hidden="true" ></i>'.__('lang_v1.update_status').'</a></li>';
                    }

                    if ($row->status == 'ordered') {
                        $html .= '<li><a href="#" data-href="'.action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'new_order']).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> '.__('lang_v1.new_order_notification').'</a></li>';
                    } elseif ($row->status == 'received') {
                        $html .= '<li><a href="#" data-href="'.action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'items_received']).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> '.__('lang_v1.item_received_notification').'</a></li>';
                    } elseif ($row->status == 'pending') {
                        $html .= '<li><a href="#" data-href="'.action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'items_pending']).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> '.__('lang_v1.item_pending_notification').'</a></li>';
                    }

                    $html .= '</ul></div>';

                    return $html;
                })
                ->removeColumn('id')
                ->editColumn('ref_no', function ($row) {
                    return ! empty($row->return_exists) ? $row->ref_no.' <small class="label bg-red label-round no-print" title="'.__('lang_v1.some_qty_returned').'"><i class="fas fa-undo"></i></small>' : $row->ref_no;
                })
                ->editColumn(
                    'final_total',
                    '<span class="final_total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                // ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->addColumn('name', function ($data) {
                    $name= $data->name . ' ' . $data->supplier_business_name;
                    $id = $data->cid??'';
                    return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
                    // return '<a href="#"  class="btn-modal edit-picking-status" data-href="' . action([\App\Http\Controllers\OrderfulfillmentController::class, 'changePickingStatus'], ['id' => $row->id]) . '"><span class="label " style="background-color:'.$color.';">' . $status . '</span></a>';
                })
                            
                ->filterColumn('name', function($query, $keyword) {
                    $query->where(function($q) use($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('added_by', function ($row) {
                    $id = $row->uid??'';
                    if ($id) {
                        
                        return '<a href="/users/'.$id.'" target="blank">  <span  class="tw-flex" data-toggle="tooltip" data-html="true" title="">'. $row->uname . '</span></a>';
                    } else {
                        return '<i class="fas fa-question"></i>';
                    }
                })                         

                ->filterColumn('added_by', function ($query, $keyword) {
                        $query->where(function ($q) use ($keyword) {
                            $q->where('u.uname', 'like', "%{$keyword}%")
                            ->orWhere('u.first_name', 'like', "%{$keyword}%")
                            ->orWhere('u.last_name', 'like', "%{$keyword}%");
                            
                        });
                })

                ->editColumn(
                    'status',
                    '<a href="#" @if(auth()->user()->can("purchase.update") || auth()->user()->can("purchase.update_status")) class="update_status no-print" data-purchase_id="{{$id}}" data-status="{{$status}}" @endif><span class="label @transaction_status($status) status-label" data-status-name="{{__(\'lang_v1.\' . $status)}}" data-orig-value="{{$status}}">{{__(\'lang_v1.\' . $status)}}
                        </span></a>'
                )
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);

                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                    }
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    $due_html = '<strong>'.__('lang_v1.purchase').':</strong> <span class="payment_due" data-orig-value="'.$due.'">'.$this->transactionUtil->num_f($due, true).'</span>';

                    if (! empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $due_html .= '<br><strong>'.__('lang_v1.purchase_return').':</strong> <a href="'.action([\App\Http\Controllers\TransactionPaymentController::class, 'show'], [$row->return_transaction_id]).'" class="view_purchase_return_payment_modal"><span class="purchase_return" data-orig-value="'.$return_due.'">'.$this->transactionUtil->num_f($return_due, true).'</span></a>';
                    }

                    return $due_html;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('purchase.view')) {
                            return  action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    }, 
                    'class' => function($row) {
                        return $row->status == 'void' ? 'text-muted' : '';
                    }])
                ->rawColumns(['final_total', 'action','added_by', 'payment_due', 'payment_status', 'status', 'ref_no', 'name'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $orderStatuses = $this->productUtil->orderStatuses();

        return view('purchase.index')
            ->with(compact('business_locations', 'suppliers', 'orderStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $taxes = TaxRate::where('business_id', $business_id)
                        ->ExcludeForTaxGroup()
                        ->get();
        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types(null, true, $business_id);

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];

        return view('purchase.create')
            ->with(compact('taxes', 'orderStatuses', 'business_locations', 'currency_details', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts', 'bl_attributes', 'common_settings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cid =  $request->contact_id;
        if ($cid) {
            $customer = Contact::find($cid);
            if($customer->contact_status=='inactive'){
                $output=[
                'success' => 0,
                'msg' => "Vendor Account is deactivated",
                ];
            return redirect()->action([\App\Http\Controllers\PurchaseController::class, 'index'])->with('status', $output);
            }
        }
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (! $this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\PurchaseController::class, 'index']));
            }

            $transaction_data = $request->only(['ref_no', 'status', 'contact_id', 'transaction_date', 'total_before_tax', 'location_id', 'discount_type', 'discount_amount', 'tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type', 'purchase_order_ids']);

            $exchange_rate = $transaction_data['exchange_rate'];

            //Reverse exchange rate and save it.
            //$transaction_data['exchange_rate'] = $transaction_data['exchange_rate'];

            //TODO: Check for "Undefined index: total_before_tax" issue
            //Adding temporary fix by validating
            $request->validate([
                'status' => 'required',
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'total_before_tax' => 'required',
                'location_id' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:'.(config('constants.document_size_limit') / 1000),
            ]);
            $user_id = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            //Update business exchange rate.
            Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['total_before_tax'], $currency_details) * $exchange_rate;

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($transaction_data['discount_type'] == 'fixed') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details) * $exchange_rate;
            } elseif ($transaction_data['discount_type'] == 'percentage') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details);
            } else {
                $transaction_data['discount_amount'] = 0;
            }

            $transaction_data['tax_amount'] = $this->productUtil->num_uf($transaction_data['tax_amount'], $currency_details) * $exchange_rate;
            $transaction_data['shipping_charges'] = $this->productUtil->num_uf($transaction_data['shipping_charges'], $currency_details) * $exchange_rate;
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;
            $tempPaidValue = $this->productUtil->num_uf($paymentAmount = $request->input('payment.0.amount'), $currency_details) * $exchange_rate;

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'purchase';
            $transaction_data['payment_status'] = 
            $transaction_data['final_total'] - $tempPaidValue == 0 ? 'paid' :
            ($transaction_data['final_total'] - $tempPaidValue == $transaction_data['final_total'] ? 'due' : 'partial');
        
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            $transaction_data['custom_field_1'] = $request->input('custom_field_1', null);
            $transaction_data['custom_field_2'] = $request->input('custom_field_2', null);
            $transaction_data['custom_field_3'] = $request->input('custom_field_3', null);
            $transaction_data['custom_field_4'] = $request->input('custom_field_4', null);
            
            $transaction_data['supplier_ref_no'] = $request->input('supplier_ref_no', null); // supplier ref no

            $transaction_data['shipping_custom_field_1'] = $request->input('shipping_custom_field_1', null);
            $transaction_data['shipping_custom_field_2'] = $request->input('shipping_custom_field_2', null);
            $transaction_data['shipping_custom_field_3'] = $request->input('shipping_custom_field_3', null);
            $transaction_data['shipping_custom_field_4'] = $request->input('shipping_custom_field_4', null);
            $transaction_data['shipping_custom_field_5'] = $request->input('shipping_custom_field_5', null);

            if ($request->input('additional_expense_value_1') != '') {
                $transaction_data['additional_expense_key_1'] = $request->input('additional_expense_key_1');
                $transaction_data['additional_expense_value_1'] = $this->productUtil->num_uf($request->input('additional_expense_value_1'), $currency_details) * $exchange_rate;
            }

            if ($request->input('additional_expense_value_2') != '') {
                $transaction_data['additional_expense_key_2'] = $request->input('additional_expense_key_2');
                $transaction_data['additional_expense_value_2'] = $this->productUtil->num_uf($request->input('additional_expense_value_2'), $currency_details) * $exchange_rate;
            }

            if ($request->input('additional_expense_value_3') != '') {
                $transaction_data['additional_expense_key_3'] = $request->input('additional_expense_key_3');
                $transaction_data['additional_expense_value_3'] = $this->productUtil->num_uf($request->input('additional_expense_value_3'), $currency_details) * $exchange_rate;
            }

            if ($request->input('additional_expense_value_4') != '') {
                $transaction_data['additional_expense_key_4'] = $request->input('additional_expense_key_4');
                $transaction_data['additional_expense_value_4'] = $this->productUtil->num_uf($request->input('additional_expense_value_4'), $currency_details) * $exchange_rate;
            }

            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
            }

            $transaction = Transaction::create($transaction_data);

            $purchase_lines = [];
            $purchases = $request->input('purchases') ?? [];
            if (!is_array($purchases)) {
                $purchases = [];
            }
            //erp Filter out purchases with null or 0 quantity
            $purchases = array_filter($purchases, function($purchase) {
                return is_array($purchase) && isset($purchase['quantity']) && !is_null($purchase['quantity']) && $purchase['quantity'] > 0;
            });
            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing);

            //Add Purchase payments
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));

            //update payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            if (! empty($transaction->purchase_order_ids)) {
                $this->transactionUtil->updatePurchaseOrderStatus($transaction->purchase_order_ids);
            }

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            $this->transactionUtil->activityLog($transaction, 'added');

            PurchaseCreatedOrModified::dispatch($transaction);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('purchase.purchase_add_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().' Line:'.$e->getLine().' Message:'.$e->getMessage());

            $msg = __('messages.something_went_wrong');
            if (config('app.debug')) {
                $msg .= ' ' . $e->getMessage();
            }
            $output = ['success' => 0, 'msg' => $msg];
        }

        return redirect('purchases')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // if (!auth()->user()->can('purchase.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $purchase = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'purchase_lines',
                                    'purchase_lines.product',
                                    'purchase_lines.product.unit',
                                    'purchase_lines.product.second_unit',
                                    'purchase_lines.variations',
                                    'purchase_lines.variations.product_variation',
                                    'purchase_lines.sub_unit',
                                    'location',
                                    'payment_lines',
                                    'tax'
                                )
                                ->firstOrFail();

        foreach ($purchase->purchase_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }

        $payment_methods = $this->productUtil->payment_types($purchase->location_id, true);

        $purchase_taxes = [];
        if (! empty($purchase->tax)) {
            if ($purchase->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {
                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }

        //Purchase orders
        $purchase_order_nos = '';
        $purchase_order_dates = '';
        if (! empty($purchase->purchase_order_ids)) {
            $purchase_orders = Transaction::find($purchase->purchase_order_ids);

            $purchase_order_nos = implode(', ', $purchase_orders->pluck('ref_no')->toArray());
            $order_dates = [];
            foreach ($purchase_orders as $purchase_order) {
                $order_dates[] = $this->transactionUtil->format_date($purchase_order->transaction_date, true);
            }
            $purchase_order_dates = implode(', ', $order_dates);
        }

        $activities = Activity::forSubject($purchase)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        $statuses = $this->productUtil->orderStatuses();
        $isLockModal = json_encode(true);

        
        return view('purchase.show')
                ->with(compact('taxes', 'isLockModal','purchase', 'payment_methods', 'purchase_taxes', 'activities', 'statuses', 'purchase_order_nos', 'purchase_order_dates'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return back()
        ->with('status', ['success' => 0,
            'msg' => 'Purchase edit not allowed', ]);
        if (! auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\PurchaseController::class, 'index']));
        }

        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (! $this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', ['success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days]), ]);
        }

        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', ['success' => 0,
                'msg' => __('lang_v1.return_exist'), ]);
        }

        $business = Business::find($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();
        $purchase = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(
                        'contact',
                        'purchase_lines',
                        'purchase_lines.product',
                        'purchase_lines.product.unit',
                        'purchase_lines.product.second_unit',
                        //'purchase_lines.product.unit.sub_units',
                        'purchase_lines.variations',
                        'purchase_lines.variations.product_variation',
                        'location',
                        'purchase_lines.sub_unit',
                        'purchase_lines.purchase_order_line'
                    )
                    ->first();

        foreach ($purchase->purchase_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }

        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];

        $purchase_orders = null;
        if (! empty($common_settings['enable_purchase_order'])) {
            $purchase_orders = Transaction::where('business_id', $business_id)
                                        ->where('type', 'purchase_order')
                                        ->where('contact_id', $purchase->contact_id)
                                        ->where(function ($q) use ($purchase) {
                                            $q->where('status', '!=', 'completed');

                                            if (! empty($purchase->purchase_order_ids)) {
                                                $q->orWhereIn('id', $purchase->purchase_order_ids);
                                            }
                                        })
                                        ->pluck('ref_no', 'id');
        }

        return view('purchase.edit')
            ->with(compact(
                'taxes',
                'purchase',
                'orderStatuses',
                'business_locations',
                'business',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts',
                'purchase_orders',
                'common_settings'
            ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cid =  $request->contact_id;
        if ($cid) {
            $customer = Contact::find($cid);
            if($customer->contact_status=='inactive'){
                $output=[
                'success' => 0,
                'msg' => "Vendor Account is deactivated",
                ];
            return redirect()->action([\App\Http\Controllers\PurchaseController::class, 'index'])->with('status', $output);
            }
        }
        if($request->input('purchases')){
            return back()
            ->with('status', ['success' => 0,
                'msg' => 'Purchase edit not allowed', ]);
        }
        if (! auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $transaction = Transaction::findOrFail($id);

            //Validate document size
            $request->validate([
                'document' => 'file|max:'.(config('constants.document_size_limit') / 1000),
            ]);

            $transaction = Transaction::findOrFail($id);
            $before_status = $transaction->status;
            $business_id = request()->session()->get('user.business_id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            $transaction_before = $transaction->replicate();

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $update_data = $request->only(['ref_no', 'status', 'contact_id',
                'transaction_date', 'total_before_tax',
                'discount_type', 'discount_amount', 'tax_id',
                'tax_amount', 'shipping_details',
                'shipping_charges', 'final_total',
                'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type', 'purchase_order_ids', ]);

            $exchange_rate = $update_data['exchange_rate'];

            //Reverse exchage rate and save
            //$update_data['exchange_rate'] = number_format(1 / $update_data['exchange_rate'], 2);

            $update_data['transaction_date'] = $this->productUtil->uf_date($update_data['transaction_date'], true);

            //unformat input values
            $update_data['total_before_tax'] = $this->productUtil->num_uf($update_data['total_before_tax'], $currency_details) * $exchange_rate;

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($update_data['discount_type'] == 'fixed') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details) * $exchange_rate;
            } elseif ($update_data['discount_type'] == 'percentage') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details);
            } else {
                $update_data['discount_amount'] = 0;
            }

            $update_data['tax_amount'] = $this->productUtil->num_uf($update_data['tax_amount'], $currency_details) * $exchange_rate;
            $update_data['shipping_charges'] = $this->productUtil->num_uf($update_data['shipping_charges'], $currency_details) * $exchange_rate;
            $update_data['final_total'] = $this->productUtil->num_uf($update_data['final_total'], $currency_details) * $exchange_rate;
            //unformat input values ends

            $update_data['custom_field_1'] = $request->input('custom_field_1', null);
            $update_data['custom_field_2'] = $request->input('custom_field_2', null);
            $update_data['custom_field_3'] = $request->input('custom_field_3', null);
            $update_data['custom_field_4'] = $request->input('custom_field_4', null);

            $update_data['shipping_custom_field_1'] = $request->input('shipping_custom_field_1', null);
            $update_data['shipping_custom_field_2'] = $request->input('shipping_custom_field_2', null);
            $update_data['shipping_custom_field_3'] = $request->input('shipping_custom_field_3', null);
            $update_data['shipping_custom_field_4'] = $request->input('shipping_custom_field_4', null);
            $update_data['shipping_custom_field_5'] = $request->input('shipping_custom_field_5', null);

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (! empty($document_name)) {
                $update_data['document'] = $document_name;
            }

            $purchase_order_ids = $transaction->purchase_order_ids ?? [];

            $update_data['additional_expense_key_1'] = $request->input('additional_expense_key_1');
            $update_data['additional_expense_key_2'] = $request->input('additional_expense_key_2');
            $update_data['additional_expense_key_3'] = $request->input('additional_expense_key_3');
            $update_data['additional_expense_key_4'] = $request->input('additional_expense_key_4');

            $update_data['additional_expense_value_1'] = $request->input('additional_expense_value_1') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_1'), $currency_details) * $exchange_rate : 0;
            $update_data['additional_expense_value_2'] = $request->input('additional_expense_value_2') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_2'), $currency_details) * $exchange_rate : 0;
            $update_data['additional_expense_value_3'] = $request->input('additional_expense_value_3') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_3'), $currency_details) * $exchange_rate : 0;
            $update_data['additional_expense_value_4'] = $request->input('additional_expense_value_4') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_4'), $currency_details) * $exchange_rate : 0;

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            //Update transaction payment status
            $payment_status = $this->transactionUtil->updatePaymentStatus($transaction->id);
            $transaction->payment_status = $payment_status;

            $purchases = $request->input('purchases');

            $delete_purchase_lines = $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $before_status);

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            $new_purchase_order_ids = $transaction->purchase_order_ids ?? [];
            $purchase_order_ids = array_merge($purchase_order_ids, $new_purchase_order_ids);
            if (! empty($purchase_order_ids)) {
                $this->transactionUtil->updatePurchaseOrderStatus($purchase_order_ids);
            }

            $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);

            PurchaseCreatedOrModified::dispatch($transaction);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('purchase.purchase_update_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];

            return back()->with('status', $output);
        }

        return redirect('purchases')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');

                //Check if return exist then not allowed
                if ($this->transactionUtil->isReturnExist($id)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.return_exist'),
                    ];

                    return $output;
                }

                $transaction = Transaction::where('id', $id)
                                ->where('business_id', $business_id)
                                ->with(['purchase_lines'])
                                ->first();

                //Check if lot numbers from the purchase is selected in sale
                if (request()->session()->get('business.enable_lot_number') == 1 && $this->transactionUtil->isLotUsed($transaction)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.lot_numbers_are_used_in_sale'),
                    ];

                    return $output;
                }

                $delete_purchase_lines = $transaction->purchase_lines;
                DB::beginTransaction();

                $log_properities = [
                    'id' => $transaction->id,
                    'ref_no' => $transaction->ref_no,
                ];
                $this->transactionUtil->activityLog($transaction, 'purchase_deleted', $log_properities);

                $transaction_status = $transaction->status;
                if ($transaction_status != 'received') {
                    $transaction->delete();
                } else {
                    //Delete purchase lines first
                    $delete_purchase_line_ids = [];
                    foreach ($delete_purchase_lines as $purchase_line) {
                        $delete_purchase_line_ids[] = $purchase_line->id;
                        $this->productUtil->decreaseProductQuantity(
                            $purchase_line->product_id,
                            $purchase_line->variation_id,
                            $transaction->location_id,
                            $purchase_line->quantity
                        );
                    }
                    PurchaseLine::where('transaction_id', $transaction->id)
                                ->whereIn('id', $delete_purchase_line_ids)
                                ->delete();

                    //Update mapping of purchase & Sell.
                    $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($transaction_status, $transaction, $delete_purchase_lines);
                }

                //Delete Transaction
                $transaction->delete();

                //Delete account transactions
                AccountTransaction::where('transaction_id', $id)->delete();

                PurchaseCreatedOrModified::dispatch($transaction, true);

                DB::commit();

                $output = ['success' => true,
                    'msg' => __('lang_v1.purchase_delete_success'),
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return $output;
    }

    /**
     * Void the specified purchase.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function voidPurchase($id)
    {
        if (! auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }
            try {
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');

                //Check if return exist then not allowed
                if ($this->transactionUtil->isReturnExist($id)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.return_exist'),
                    ];

                    return $output;
                }

                $transaction = Transaction::where('id', $id)
                                ->where('business_id', $business_id)
                                ->with(['purchase_lines'])
                                ->first();
                
                // if($totalPaid > 0){
                //     $output = [
                //         'success' => false,
                //         'msg' => __('lang_v1.purchase_already_paid'),
                //     ];
                //     return $output;
                // }
                //Check if lot numbers from the purchase is selected in sale
                if (request()->session()->get('business.enable_lot_number') == 1 && $this->transactionUtil->isLotUsed($transaction)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.lot_numbers_are_used_in_sale'),
                    ];

                    return $output;
                }

                $purchase_lines = $transaction->purchase_lines;
                DB::beginTransaction();

                $log_properities = [
                    'id' => $transaction->id,
                    'ref_no' => $transaction->ref_no,
                ];
                $this->transactionUtil->activityLog($transaction, 'purchase_voided', $log_properities);

                $transaction_status = $transaction->status;
                if ($transaction_status == 'received') {
                    //Decrease product quantity if purchase was received
                    foreach ($purchase_lines as $purchase_line) {
                        // Make quantity negative
                        // $purchase_line->quantity = -1 * abs($purchase_line->quantity);
                        // $purchase_line->save();

                        // Update product stock with negative quantity
                        $this->productUtil->decreaseProductQuantity(
                            $purchase_line->product_id,
                            $purchase_line->variation_id,
                            $transaction->location_id,
                            $purchase_line->quantity,
                        );
                        // abs($purchase_line->quantity),
                    }

                    //Update mapping of purchase & Sell
                    $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($transaction_status, $transaction, $purchase_lines);
                }
                $totalPaid = TransactionPayment::where('transaction_id', $transaction->id)->where('amount', '!=', 0)->sum('amount');
                // add total paid to wallet 
                $contact = Contact::find($transaction->contact_id);
                $contact->balance += $totalPaid;
                $contact->save();
                
                $paymentData = [
                    'transaction_id' => null,
                    'business_id' => $business_id,
                    'amount' => $totalPaid,
                    'payment_type'=>'debit',
                    'method' => 'cash',
                    'card_type' => 'credit',
                    'payment_ref_no' => 'Void',
                    'paid_on' => now(),
                    'payment_for' => $contact->id,
                    'gateway' => '',
                    'note' => "{$transaction->ref_no} was voided and {$totalPaid} was added to wallet as Advance Payment",
                    'created_by' => auth()->user()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('transaction_payments')->insert($paymentData);
                // delete payments
                $deletePayments = TransactionPayment::where('transaction_id', $transaction->id)->where('amount', '!=', 0)->delete();
                
                $statusbefore = $transaction->status;
                // reduce amount in final_total which was not paid 
                // $totalPaid = TransactionPayment::where('transaction_id', $transaction->id)->where('amount', '!=', 0)->sum('amount');
                $transaction->final_total = 0;
                $transaction->status = 'void';
                $transaction->save();

                // activity 
                activity()->performedOn($transaction)  ->causedBy(auth()->user())
                ->withProperties([
                    'customized' => [
                        'old_value' => $statusbefore,
                        'new_value' => $transaction->status,
                        'mid' => ' updated status ',
                        'modal_ref_no' => $transaction->ref_no,
                    ]
                    ])
                ->useLog('purchase_voided')
                ->tap(function($activity) {
                    $activity->business_id = request()->session()->get('user.business_id');
                    $activity->save();
                })
                ->log('purchase_voided');

                PurchaseCreatedOrModified::dispatch($transaction, true);

                DB::commit();

                $output = ['success' => true,
                    'msg' => __('Purchase Voided Successfully'),
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return $output;
    }

    /**
     * Get location for supplier filtering
     * Auto-detect for regular users, allow selection for super admins
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int|null
     */
    private function getLocationForSupplier($request)
    {
        $user = auth()->user();
        $business_id = $request->session()->get('user.business_id');
        
        // Check if user is super admin or has access to all locations
        $is_super_admin = $user->can('access_all_locations') || $user->can('admin');
        
        if ($is_super_admin) {
            // Super admin can choose location from request
            $location_id = $request->input('location_id');
            if (!empty($location_id)) {
                return $location_id;
            }
            // If no location selected by super admin, return null to show all suppliers
            return null;
        }
        
        // For regular users, auto-detect location
        $permitted_locations = $user->permitted_locations($business_id);
        
        if ($permitted_locations == 'all') {
            // User has access to all locations, get first available location
            $default_location = \App\BusinessLocation::where('business_id', $business_id)
                ->where('is_active', 1)
                ->first();
            return $default_location ? $default_location->id : null;
        } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
            // User has specific location permissions, use the first one
            return $permitted_locations[0];
        }
        
        return null;
    }

    /**
     * Retrieves supliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            
            // Get location for filtering
            if(auth()->user()->can('access_all_locations')){
                $location_id = null;
                if(request()->has('location_id')){
                    $location_id = request()->input('location_id');
                }
            }else{
                $location_id = $this->getLocationForSupplier(request());
            }

            $query = Contact::where('business_id', $business_id)
                            ->active();
                            
            // Filter by location if provided
            if (!empty($location_id)) {
                $query->where('location_id', $location_id);
            }

            $suppliers = $query->where(function ($query) use ($term) {
                $query->where('name', 'like', '%'.$term.'%')
                                ->orWhere('supplier_business_name', 'like', '%'.$term.'%')
                                ->orWhere('contacts.contact_id', 'like', '%'.$term.'%');
            })
                        ->select(
                            'contacts.id',
                            DB::raw('IF(name="", supplier_business_name, name) as text'),
                            'supplier_business_name as business_name',
                            'contacts.mobile',
                            'contacts.address_line_1',
                            'contacts.address_line_2',
                            'contacts.city',
                            'contacts.state',
                            'contacts.country',
                            'contacts.zip_code',
                            'contacts.contact_id',
                            'contacts.pay_term_type',
                            'contacts.pay_term_number',
                            'contacts.balance',
                            'contacts.location_id'
                        )
                        ->onlySuppliers()
                        ->get();

            return json_encode($suppliers);
        }
    }

    /**
     * Retrieves suppliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliersAuto()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = Contact::where('business_id', $business_id)->where('id',$term)
                            ->active()
                        ->select(
                            'contacts.id',
                            DB::raw('IF(name="", supplier_business_name, name) as text'),
                            'supplier_business_name as business_name',
                            'contacts.mobile',
                            'contacts.address_line_1',
                            'contacts.address_line_2',
                            'contacts.city',
                            'contacts.state',
                            'contacts.country',
                            'contacts.zip_code',
                            'contacts.contact_id',
                            'contacts.pay_term_type',
                            'contacts.pay_term_number',
                            'contacts.balance',
                            'contacts.location_id'
                        )
                        ->onlySuppliers()
                        ->get();

            return json_encode($query);
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        if (request()->ajax()) {
            $term = request()->term;

            $check_enable_stock = true;
            if (isset(request()->check_enable_stock)) {
                $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
            }

            $only_variations = false;
            if (isset(request()->only_variations)) {
                $only_variations = filter_var(request()->only_variations, FILTER_VALIDATE_BOOLEAN);
            }

            if (empty($term)) {
                return json_encode([]);
            }
            $search_fields = request()->search_fields;
            if(empty($search_fields)){
                $search_fields = ['sku'];
            }
            $isParent = filter_var(request()->isParent, FILTER_VALIDATE_BOOLEAN);
            $business_id = request()->session()->get('user.business_id');
            // now search in sku and if name then search in name 
            if($isParent){
                $result = Product::where('business_id', $business_id)
                ->where(function ($query) use ($term, $search_fields) {
                    $query->when(in_array('name', $search_fields), function ($query) use ($term) {
                        $query->where('products.name', 'like', '%'.$term.'%');
                    })
                    ->when(in_array('sku', $search_fields), function ($query) use ($term) {
                        $query->orWhere('sku', 'like', '%'.$term.'%');
                    }) ;
                })
                ->select('id', 'name', 'sku')
                ->get();
                $result = $result->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'text' =>  $product->sku . ' - ' . $product->name,
                        'product_id' => $product->id,
                        'variation_id' => 0,
                        'type' => $product->type,
                    ];
                });
                
                return json_encode($result);
            }
            $q = Product::leftJoin(
                'variations',
                'products.id',
                '=',
                'variations.product_id'
            )
                ->where(function ($query) use ($term, $search_fields) {
                    $query->when(in_array('name', $search_fields), function ($query) use ($term) {
                        $query->where('products.name', 'like', '%'.$term.'%');
                    })
                    ->when(in_array('sku', $search_fields), function ($query) use ($term) {
                        $query->orWhere('sku', 'like', '%'.$term.'%');
                    })
                    ->when(in_array('sub_sku', $search_fields), function ($query) use ($term) {
                        $query->orWhere('sub_sku', 'like', '%'.$term.'%');
                    })
                    ->when(in_array('var_barcode_no', $search_fields), function ($query) use ($term) {
                        $query->orWhere('var_barcode_no', 'like', '%'.$term.'%');
                    });
                })
                ->active()
                ->where('business_id', $business_id)
                ->whereNull('variations.deleted_at')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku'
                )
                ->groupBy('variation_id');

            if ($check_enable_stock) {
                $q->where('enable_stock', 1);
            }
            if (! empty(request()->location_id)) {
                $q->ForLocation(request()->location_id);
            }
            $products = $q->get();

            $products_array = [];
            foreach ($products as $product) {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['variations'][]
                = [
                    'variation_id' => $product->variation_id,
                    'variation_name' => $product->variation,
                    'sub_sku' => $product->sub_sku,
                ];
            }

            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (! empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    if ($no_of_records > 1 && $value['type'] != 'single' && ! $only_variations) {
                        $result[] = ['id' => $i,
                            'text' =>$value['sku']. ' - '.$value['name'],
                            'variation_id' => 0,
                            'product_id' => $key,
                            'type' => $value['type'],
                        ];
                    }
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            $text = $text.' ('.$variation['variation_name'].')';
                        }
                        $i++;
                        $result[] = ['id' => $i,
                            'text' =>$variation['sub_sku'].' - '.$text,
                            'product_id' => $key,
                            'variation_id' => $variation['variation_id'],
                            'type' => $value['type'],
                        ];
                    }
                    $i++;
                }
            }

            return json_encode($result);
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseEntryRow(Request $request)
    {
        if (request()->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = request()->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $is_purchase_order = $request->has('is_purchase_order');
            $supplier_id = $request->input('supplier_id');

            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            if (! empty($product_id)) {
                $row_count = $request->input('row_count');
                $product = Product::where('id', $product_id)
                                    ->with(['unit', 'second_unit'])
                                    ->first();

                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);
                $sub_units = [];
                    if ($product->unit) {
                        $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);
                    }
                $query = Variation::where('product_id', $product_id)
                                ->with([
                                    'product_variation',
                                    'variation_location_details' => function ($q) use ($location_id) {
                                        $q->where('location_id', $location_id);
                                    },
                                ]);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations = $query->get();
                $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

                $last_purchase_line = $this->getLastPurchaseLine($variation_id, $location_id, $supplier_id);

                return view('purchase.partials.purchase_entry_row')
                    ->with(compact(
                        'product',
                        'variations',
                        'row_count',
                        'variation_id',
                        'taxes',
                        'currency_details',
                        'hide_tax',
                        'sub_units',
                        'is_purchase_order',
                        'last_purchase_line'
                    ));
            }
        }
    }

    public function getPurchaseEntryRowPopup(Request $request)
    {
        if (request()->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = request()->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $is_purchase_order = $request->has('is_purchase_order');
            $supplier_id = $request->input('supplier_id');

            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            if (! empty($product_id)) {
                $row_count = $request->input('row_count');
                $product = Product::where('id', $product_id)
                                    ->with(['unit', 'second_unit'])
                                    ->first();

                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);
                $sub_units = [];
                if ($product->unit) {
                    $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);
                }
                $query = Variation::where('product_id', $product_id)
                                ->with([
                                    'product_variation',
                                    'variation_location_details' => function ($q) use ($location_id) {
                                        $q->where('location_id', $location_id);
                                    },
                                ]);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations = $query->get();
                $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                    ->get();

                $last_purchase_line = $this->getLastPurchaseLine($variation_id, $location_id, $supplier_id);
                $stock_qty = !empty($variations[0]->variation_location_details->first()) ? (int) $variations[0]->variation_location_details->first()->qty_available : 0;
                return response()->json([
                    'product' => $product,
                    'variations' => $variations,
                    'row_count' => $row_count,
                    'variation_id' => $variation_id,
                    'taxes' => $taxes,
                    'currency_details' => $currency_details,
                    'hide_tax' => $hide_tax,
                    'sub_units' => $sub_units,
                    'is_purchase_order' => $is_purchase_order,
                    'last_purchase_line' => $last_purchase_line,
                    'stock_qty'=>$stock_qty,
                ]);
            }
        }
    }
    public function getPurchaseEntryRowMatrix($variation_ids, $location_id)
    {
        $output = [];

        try {
            $product_id = request()->get('product_id');
            $business_id = request()->session()->get('user.business_id');
            $location_id = request()->get('location_id');
            $supplier_id = request()->get('supplier_id');
            $is_purchase_order = request()->has('is_purchase_order');

            $row_count = request()->get('product_row');
            $quantities = request()->get('quantities', []);
            $weighing_barcode = request()->get('weighing_scale_barcode', null);

            $variation_ids_array = explode(',', $variation_ids);
            $rows = [];
            $quantities = explode(',', $quantities); //
            foreach ($variation_ids_array as $index => $variation_id) {
                $quantity = isset($quantities[$index]) ? $quantities[$index] : 1;
                if ($variation_id == 'null' && !empty($weighing_barcode)) {
                    $product_details = $this->__parseWeighingBarcode($weighing_barcode);
                    if ($product_details['success']) {
                        $variation_id = $product_details['variation_id'];
                        $quantity = $product_details['qty'];
                    } else {
                        $output['success'] = false;
                        $output['msg'] = $product_details['msg'];

                        return $output;
                    }
                }
                \Log::info($quantity);
                 $hide_tax = 'hide';
            if (request()->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }
                $sell_row = $this->getPurchaseLineRow($variation_id, $location_id, $quantity, $row_count ,$product_id,$business_id,$supplier_id ,$hide_tax ,$is_purchase_order);
                $rows[] = $sell_row;
                $row_count++; 
            }

            $output['rows'] = $rows;
            $output['success'] = true;
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = __('lang_v1.item_out_of_stock');
        }

        return $output;
    }
    private function getPurchaseLineRow($variation_id, $location_id, $quantity, $row_count ,$product_id,$business_id,$supplier_id ,$hide_tax ,$is_purchase_order)
    {
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            if (! empty($product_id)) {
                $product = Product::where('id', $product_id)
                                    ->with(['unit', 'second_unit'])
                                    ->first();
                  $output['success'] = true;
                  $output['enable_sr_no'] = $product->enable_sr_no;

                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);

                $query = Variation::where('product_id', $product_id)
                                ->with([
                                    'product_variation',
                                    'variation_location_details' => function ($q) use ($location_id) {
                                        $q->where('location_id', $location_id);
                                    },
                                ]);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations = $query->get();
                $taxes = TaxRate::where('business_id', $business_id)->ExcludeForTaxGroup()->get();
                $last_purchase_line = $this->getLastPurchaseLine($variation_id, $location_id, $supplier_id);

                $output['html_content'] = view('purchase.partials.purchase_entry_row')
                ->with(compact(
                        'product',
                        'variations',
                        'row_count',
                        'variation_id',
                        'taxes',
                        'currency_details',
                        'hide_tax',
                        'sub_units',
                        'is_purchase_order',
                        'last_purchase_line',
                        'quantity'

                    ))
                    ->render();
            }
            return $output;
    }

    /**
     * Finds last purchase line of a variation for the supplier for a location
     */
    private function getLastPurchaseLine($variation_id, $location_id, $supplier_id = null)
    {
        $query = PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id',
                        '=', 't.id')
                        ->where('t.location_id', $location_id)
                        ->where('t.type', 'purchase')
                        ->where('t.status', 'received')
                        ->where('purchase_lines.variation_id', $variation_id);

        if (! empty($supplier_id)) {
            $query = $query->where('t.contact_id', '=', $supplier_id);
        }
        $purchase_line = $query->orderBy('transaction_date', 'desc')
                            ->select('purchase_lines.*')
                            ->first();

        return $purchase_line;
    }

    /**
     * Import purchase products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importPurchaseProducts(Request $request)
    {
        try {
            $file = $request->file('file');

            $parsed_array = Excel::toArray([], $file);
            //Remove header row
            $imported_data = array_splice($parsed_array[0], 1);

            $business_id = $request->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $row_count = $request->input('row_count');

            $formatted_data = [];
            $row_index = 0;
            $error_msg = '';
            foreach ($imported_data as $key => $value) {
                $row_index = $key + 1;
                $temp_array = [];

                if (! empty($value[0])) {
                    $variation = Variation::where('sub_sku', trim($value[0]))
                                        ->join('products', 'products.id', '=', 'variations.product_id')
                                        ->where('products.business_id', $business_id) 
                                        ->with([
                                            'product_variation',
                                            'variation_location_details' => function ($q) use ($location_id) {
                                                $q->where('location_id', $location_id);
                                            },
                                        ])
                                        ->select('variations.*')
                                        ->first();
                    $temp_array['variation'] = $variation;

                    if (empty($variation)) {
                        $error_msg = __('lang_v1.product_not_found_exception', ['row' => $row_index, 'sku' => $value[0]]);
                        break;
                    }

                    $product = Product::where('id', $variation->product_id)
                                    ->where('business_id', $business_id)
                                    ->with(['unit'])
                                    ->first();

                    if (empty($product)) {
                        $error_msg = __('lang_v1.product_not_found_exception', ['row' => $row_index, 'sku' => $value[0]]);
                        break;
                    }

                    $temp_array['product'] = $product;

                    $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product->id);
                    $sub_units = [];
                    if ($product->unit) {
                        $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product->id);
                    }
                    $temp_array['sub_units'] = $sub_units;
                } else {
                    $error_msg = __('lang_v1.product_not_found_exception', ['row' => $row_index, 'sku' => $value[0]]);
                    break;
                }

                if (! empty($value[0])) {
                    $temp_array['quantity'] = $value[1];
                } else {
                    $error_msg = __('lang_v1.quantity_required', ['row' => $row_index]);
                    break;
                }

                $temp_array['unit_cost_before_discount'] = ! empty($value[2]) ? $value[2] : $variation->default_purchase_price;
                $temp_array['discount_percent'] = ! empty($value[3]) ? $value[3] : 0;
                $temp_array['row_discount_type'] = ! empty($value[4]) ? $value[4] : 'fixed';
                $tax_id = null;

                if (! empty($value[4])) {
                    $tax_name = trim($value[4]);
                    $tax = TaxRate::where('business_id', $business_id)
                                ->where('name', 'like', "%{$tax_name}%")
                                ->first();

                    $tax_id = $tax->id ?? $tax_id;
                }

                $temp_array['tax_id'] = $tax_id;
                $temp_array['lot_number'] = ! empty($value[5]) ? $value[5] : null;
                $temp_array['mfg_date'] = ! empty($value[6]) ? $this->productUtil->format_date($value[6]) : null;
                $temp_array['exp_date'] = ! empty($value[7]) ? $this->productUtil->format_date($value[7]) : null;

                $formatted_data[] = $temp_array;
            }

            if (! empty($error_msg)) {
                return [
                    'success' => false,
                    'msg' => $error_msg,
                ];
            }

            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $html = view('purchase.partials.imported_purchase_product_rows')
                        ->with(compact('formatted_data', 'taxes', 'currency_details', 'hide_tax', 'row_count'))->render();

            return [
                'success' => true,
                'msg' => __('lang_v.imported'),
                'html' => $html,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }
    }

    /**
     * Summary of getPurchaseOrderLines
     * @param mixed $purchase_order_id
     * @return array{html: string, po: \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model}
     */
    public function getPurchaseOrderLines($purchase_order_id)
    {
        $business_id = request()->session()->get('user.business_id');

        $purchase_order = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase_order')
                        ->with(['purchase_lines', 'purchase_lines.variations',
                            'purchase_lines.product', 'purchase_lines.product.unit', 'purchase_lines.variations.product_variation', ])
                        ->findOrFail($purchase_order_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

        $sub_units_array = [];
        foreach ($purchase_order->purchase_lines as $pl) {
            $sub_units_array[$pl->id] = $this->productUtil->getSubUnits($business_id, $pl->product->unit->id, false, $pl->product_id);
            $sub_units_array[$pl->id] = [];
            if ($pl->product->unit) {
                $sub_units_array[$pl->id] = $this->productUtil->getSubUnits($business_id, $pl->product->unit->id, false, $pl->product_id);
            }
        }
        $hide_tax = request()->session()->get('business.enable_inline_tax') == 1 ? '' : 'hide';
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $row_count = request()->input('row_count');

        $html = view('purchase.partials.purchase_order_lines')
                ->with(compact(
                    'purchase_order',
                    'taxes',
                    'hide_tax',
                    'currency_details',
                    'row_count',
                    'sub_units_array'
                ))->render();

        return [
            'html' => $html,
            'po' => $purchase_order,
        ];
    }

    
    public function updateRefNo(Request $request, $id)
    {
        $purchase = Transaction::find($id);
        $old_purchase = $purchase->replicate();
        
        $purchase->supplier_ref_no = $request->input('supplier_ref_no');
        $purchase->save();

        // custom activity log , own activity log
        activity()
        ->performedOn($purchase)
        ->causedBy(auth()->user())
        ->withProperties([
        'customized' => [
            'old_value' => $old_purchase->supplier_ref_no,
            'new_value' => $purchase->supplier_ref_no,
            'mid' => ' changed to ',
            'modal_ref_no' => $purchase->ref_no,
        ]
        ])
        ->useLog('supplier_ref_no_updated')
        ->tap(function($activity) {
            $activity->business_id = request()->session()->get('user.business_id');
            $activity->save();
        })
        ->log('supplier_ref_no_updated');

        return response()->json(['success' => true, 'msg' => 'Supplier reference number updated successfully']);
    }
    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkRefNumber(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $contact_id = $request->input('contact_id');
        $ref_no = $request->input('ref_no');
        $purchase_id = $request->input('purchase_id');

        $count = 0;
        if (! empty($contact_id) && ! empty($ref_no)) {
            //check in transactions table
            $query = Transaction::where('business_id', $business_id)
                            ->where('ref_no', $ref_no);
            if (! empty($purchase_id)) {
                $query->where('id', '!=', $purchase_id);
            }
            $count = $query->count();
        }
        if ($count == 0) {
            echo 'true';
            exit;
        } else {
            echo 'false';
            exit;
        }
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $taxes = TaxRate::where('business_id', $business_id)
                                ->pluck('name', 'id');
            $purchase = Transaction::where('business_id', $business_id)
                                    ->where('id', $id)
                                    ->with(
                                        'contact',
                                        'purchase_lines',
                                        'purchase_lines.product',
                                        'purchase_lines.variations',
                                        'purchase_lines.variations.product_variation',
                                        'location',
                                        'payment_lines'
                                    )
                                    ->first();
            $payment_methods = $this->productUtil->payment_types(null, false, $business_id);


            foreach ($purchase->purchase_lines as $key => $value) {
                if (! empty($value->sub_unit_id)) {
                    $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                    $purchase->purchase_lines[$key] = $formated_purchase_line;
                }
            }
            $invoice_layout = $this->businessUtil->invoiceLayout(1, 1);

            //Purchase orders
            $purchase_order_nos = '';
            $purchase_order_dates = '';
            $purchase->logo = $invoice_layout->show_logo != 0 && ! empty($invoice_layout->logo) && file_exists(public_path('uploads/invoice_logos/' . $invoice_layout->logo)) ? asset('uploads/invoice_logos/' . $invoice_layout->logo) : false;
            $purchase->footer_text = $invoice_layout->footer_text;
            $purchase->show_barcode = $invoice_layout->show_barcode;
            $purchase->show_qr_code = $invoice_layout->show_qr_code;
            $purchase->qr_code_text = $invoice_layout->qr_code_text;
            $purchase->barcode_text = $invoice_layout->barcode_text;
            $purchase->barcode_type = $invoice_layout->barcode_type;
            $purchase->barcode_size = $invoice_layout->barcode_size;
            $purchase->additional_notes = $invoice_layout->additional_notes;

            if (! empty($purchase->purchase_order_ids)) {
                $purchase_orders = Transaction::find($purchase->purchase_order_ids);

                $purchase_order_nos = implode(', ', $purchase_orders->pluck('ref_no')->toArray());
                $order_dates = [];
                foreach ($purchase_orders as $purchase_order) {
                    $order_dates[] = $this->transactionUtil->format_date($purchase_order->transaction_date, true);
                }
                $purchase_order_dates = implode(', ', $order_dates);
            }

            $output = ['success' => 1, 'receipt' => [], 'print_title' => $purchase->ref_no];
            $output['receipt']['html_content'] = view('purchase.receipt.print_purchase_invoice', compact('taxes', 'purchase', 'payment_methods', 'purchase_order_nos', 'purchase_order_dates'))->render();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Update purchase status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        if (! auth()->user()->can('purchase.update') && ! auth()->user()->can('purchase.update_status')) {
            abort(403, 'Unauthorized action.');
        }
        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (! $this->transactionUtil->canBeEdited($request->input('purchase_id'), $edit_days)) {
            return ['success' => 0,
                'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days]), ];
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                                ->where('type', 'purchase')
                                ->with(['purchase_lines'])
                                ->findOrFail($request->input('purchase_id'));
            
            $before_status = $transaction->status;
            if($before_status == 'void'){
                $output = ['success' => 0,
                'msg' => 'This is Voided PR',
                ];
                return $output;  
            }
            $update_data['status'] = $request->input('status');

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            foreach ($transaction->purchase_lines as $purchase_line) {
                $this->productUtil->updateProductStock($before_status, $transaction, $purchase_line->product_id, $purchase_line->variation_id, $purchase_line->quantity, $purchase_line->quantity, $currency_details);
            }

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, null);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('purchase.purchase_update_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        return $output;
    }

    /**
     * Import Purchase from CSV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importFromCsv(Request $request)
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            //Check if subscribed or not
            if (! $this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\PurchaseController::class, 'index']));
            }

            if (! $request->hasFile('csv_file')) {
                $output = ['success' => 0,
                    'msg' => __('lang_v1.file') . ' ' . __('lang_v1.is_required'),
                ];

                return redirect()->action([\App\Http\Controllers\PurchaseController::class, 'index'])->with('status', $output);
            }

            $file = $request->file('csv_file');
            $location_id = $request->input('location_id');
            $contact_id = $request->input('contact_id');

            if (empty($location_id)) {
                $output = ['success' => 0,
                    'msg' => __('purchase.business_location') . ' ' . __('lang_v1.is_required'),
                ];

                return redirect()->action([\App\Http\Controllers\PurchaseController::class, 'index'])->with('status', $output);
            }

            if (empty($contact_id)) {
                $output = ['success' => 0,
                    'msg' => __('purchase.supplier') . ' ' . __('lang_v1.is_required'),
                ];

                return redirect()->action([\App\Http\Controllers\PurchaseController::class, 'index'])->with('status', $output);
            }

            // Get selected supplier
            $supplier = Contact::where('business_id', $business_id)
                ->where('id', $contact_id)
                ->whereIn('type', ['supplier', 'both'])
                ->active()
                ->first();

            if (empty($supplier)) {
                $output = ['success' => 0,
                    'msg' => 'Selected supplier not found or is inactive.',
                ];

                return redirect()->action([\App\Http\Controllers\PurchaseController::class, 'index'])->with('status', $output);
            }

            // Parse CSV file
            $parsed_array = Excel::toArray([], $file);
            
            // Get data rows (skip header if it looks like a header)
            $imported_data = $parsed_array[0];
            
            // Check if first row is header (contains 'sku' or 'quantity' text)
            if (!empty($imported_data[0])) {
                $first_row = array_map('strtolower', array_map('trim', $imported_data[0]));
                if (in_array('sku', $first_row) || in_array('quantity', $first_row) || in_array('qty', $first_row)) {
                    array_shift($imported_data); // Remove header row
                }
            }

            $purchase_lines_data = [];
            $errors = [];
            $row_no = 1;

            foreach ($imported_data as $key => $row) {
                $row_no = $key + 2; // +2 because we removed header and array is 0-indexed
                
                // Skip empty rows
                if (empty($row[0]) && empty($row[1])) {
                    continue;
                }

                $sku = ! empty($row[0]) ? trim($row[0]) : '';
                $quantity = ! empty($row[1]) ? trim($row[1]) : 0;

                if (empty($sku)) {
                    $errors[] = __('lang_v1.row') . ' ' . $row_no . ': SKU ' . __('lang_v1.is_required');
                    continue;
                }

                if (empty($quantity) || $quantity <= 0) {
                    $errors[] = __('lang_v1.row') . ' ' . $row_no . ': ' . __('lang_v1.quantity') . ' ' . __('lang_v1.is_required');
                    continue;
                }

                // Try to find variation by sub_sku first
                $variation = Variation::where('sub_sku', $sku)
                    ->join('products AS P', 'variations.product_id', '=', 'P.id')
                    ->where('P.business_id', $business_id)
                    ->whereNull('variations.deleted_at')
                    ->select('variations.id as variation_id', 'P.id as product_id', 'variations.default_purchase_price', 'variations.dpp_inc_tax')
                    ->first();

                // If not found, try to find by product SKU (for single products)
                if (empty($variation)) {
                    $product = Product::where('sku', $sku)
                        ->where('business_id', $business_id)
                        ->where('type', 'single')
                        ->first();

                    if (! empty($product)) {
                        // Get the default variation for this product
                        $variation = Variation::where('variations.product_id', $product->id)
                            ->whereNull('variations.deleted_at')
                            ->join('products AS P', 'variations.product_id', '=', 'P.id')
                            ->select('variations.id as variation_id', 'P.id as product_id', 'variations.default_purchase_price', 'variations.dpp_inc_tax')
                            ->first();
                    }
                }

                if (empty($variation)) {
                    $errors[] = __('lang_v1.row') . ' ' . $row_no . ': Product with SKU "' . $sku . '" not found';
                    continue;
                }

                $quantity = $this->productUtil->num_uf($quantity);
                $purchase_price = !empty($variation->dpp_inc_tax) ? $variation->dpp_inc_tax : (!empty($variation->default_purchase_price) ? $variation->default_purchase_price : 0);
                $purchase_price_exc_tax = !empty($variation->default_purchase_price) ? $variation->default_purchase_price : 0;

                $purchase_lines_data[] = [
                    'product_id' => $variation->product_id,
                    'variation_id' => $variation->variation_id,
                    'quantity' => $quantity,
                    'purchase_price' => $purchase_price_exc_tax,
                    'purchase_price_inc_tax' => $purchase_price,
                    'pp_without_discount' => $purchase_price_exc_tax,
                    'item_tax' => 0,
                    'purchase_line_tax_id' => null,
                    'discount_percent' => 0,
                    'row_discount_type' => 'fixed',
                    'secondary_unit_quantity' => 0,
                ];
            }

            if (! empty($errors)) {
                $output = ['success' => 0,
                    'msg' => implode('<br>', $errors),
                ];

                return redirect()->action([\App\Http\Controllers\PurchaseController::class, 'index'])->with('status', $output);
            }

            if (empty($purchase_lines_data)) {
                $output = ['success' => 0,
                    'msg' => 'No valid data found in CSV file.',
                ];

                return redirect()->action([\App\Http\Controllers\PurchaseController::class, 'index'])->with('status', $output);
            }

            DB::beginTransaction();

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $user_id = request()->session()->get('user.id');

            // Calculate totals
            $total_before_tax = 0;
            foreach ($purchase_lines_data as $line) {
                $total_before_tax += $line['purchase_price_inc_tax'] * $line['quantity'];
            }

            // Create transaction data
            $transaction_data = [
                'business_id' => $business_id,
                'location_id' => $location_id,
                'contact_id' => $supplier->id,
                'type' => 'purchase',
                'status' => 'received',
                'created_by' => $user_id,
                'transaction_date' => \Carbon::now()->toDateTimeString(),
                'total_before_tax' => $total_before_tax,
                'tax_amount' => 0,
                'discount_type' => 'fixed',
                'discount_amount' => 0,
                'final_total' => $total_before_tax,
                'payment_status' => 'due',
                'exchange_rate' => 1,
            ];

            // Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            // Generate reference number
            $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);

            $transaction = Transaction::create($transaction_data);

            // Create purchase lines
            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchase_lines_data, $currency_details, 0);

            // Update payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            // Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            $this->transactionUtil->activityLog($transaction, 'added');

            PurchaseCreatedOrModified::dispatch($transaction);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('purchase.purchase_add_success') . ' (' . count($purchase_lines_data) . ' items)',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage(),
            ];
        }

        return redirect()->action([\App\Http\Controllers\PurchaseController::class, 'index'])->with('status', $output);
    }

    /**
     * Download CSV template for Purchase import
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadCsvTemplate()
    {
        // Create CSV content with header
        $csv_data = [
            ['SKU', 'Quantity'],
            ['SKU001', '10'],
            ['SKU002', '5'],
        ];

        $filename = 'purchase_import_template.csv';
        
        // Create temporary file
        $file = fopen('php://temp', 'r+');
        
        // Write CSV data
        foreach ($csv_data as $row) {
            fputcsv($file, $row);
        }
        
        rewind($file);
        $csv_content = stream_get_contents($file);
        fclose($file);

        // Return download response
        return response($csv_content, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
