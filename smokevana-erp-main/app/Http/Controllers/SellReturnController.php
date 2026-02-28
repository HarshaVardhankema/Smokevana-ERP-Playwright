<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Events\TransactionPaymentDeleted;
use App\Models\TransactionReturnEcom;
use App\ShipStation;
use App\TaxRate;
use App\Transaction;
use App\TransactionSellLine;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class SellReturnController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $transactionUtil;

    protected $contactUtil;

    protected $businessUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ContactUtil $contactUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                    ->join(
                        'business_locations AS bl',
                        'transactions.location_id',
                        '=',
                        'bl.id'
                    )
                    ->join(
                        'transactions as T1',
                        'transactions.return_parent_id',
                        '=',
                        'T1.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return')
                    ->where('transactions.status', 'final')
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.invoice_no',
                        'contacts.name',
                        'contacts.id as cid',
                        'contacts.supplier_business_name',
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.invoice_no as parent_sale',
                        'T1.id as parent_sale_id',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (! empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                        ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="btn-group dropdown scroll-safe-dropdown">
                    <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info tw-w-max dropdown-toggle" 
                        data-toggle="dropdown" aria-expanded="false">'.
                        __('messages.actions').
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li><a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'App\Http\Controllers\SellReturnController@show\', [$parent_sale_id])}}"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</a></li>
                        <li><a href="{{action(\'App\Http\Controllers\SellReturnController@add\', [$parent_sale_id,"cid=sells_return"])}}" ><i class="fa fa-edit" aria-hidden="true"></i> @lang("messages.edit")</a></li>
                        <li><a href="{{action(\'App\Http\Controllers\SellReturnController@destroy\', [$id])}}" class="delete_sell_return" ><i class="fa fa-trash" aria-hidden="true"></i> @lang("messages.delete")</a></li>
                        <li><a href="#" class="print-invoice" data-href="{{action(\'App\Http\Controllers\SellReturnController@printInvoice\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a></li>

                    @if($payment_status != "paid")
                        <li><a href="{{action(\'App\Http\Controllers\TransactionPaymentController@addPayment\', [$id])}}" class="add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a></li>
                    @endif

                    <li><a href="{{action(\'App\Http\Controllers\TransactionPaymentController@show\', [$id])}}" class="view_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.view_payments")</a></li>
                    </ul>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('parent_sale', function ($row) {
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->parent_sale_id]).'">'.$row->parent_sale.'</button>';
                })
                // ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->addColumn('name', function ($data) {
                    $name= $data->name . ' ' . $data->supplier_business_name;
                    $id = $data->cid??'';
                    return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
                    // return '<a href="#"  class="btn-modal edit-picking-status" data-href="' . action([\App\Http\Controllers\OrderfulfillmentController::class, 'changePickingStatus'], ['id' => $row->id]) . '"><span class="label " style="background-color:'.$color.';">' . $status . '</span></a>';
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })                
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'show\'], [$id])}}" class="view_payment_modal payment-status payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}</span></a>'
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;

                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="'.$due.'">'.$due.'</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view')) {
                            return  action([\App\Http\Controllers\SellReturnController::class, 'show'], [$row->parent_sale_id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'action', 'parent_sale', 'payment_status', 'payment_due', 'name'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, true);

        return view('sell_return.index')->with(compact('business_locations', 'customers', 'sales_representative'));
    }
    public function indexEcomPending()
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS bl',
                        'transactions.location_id',
                        '=',
                        'bl.id'
                    )
                    ->join(
                        'transactions as T1',
                        'transactions.return_parent_id',
                        '=',
                        'T1.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return_ecom')
                    ->where('transactions.status', 'pending')
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.invoice_no',
                        'contacts.name',
                        'contacts.id as cid',
                        'contacts.supplier_business_name',
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.invoice_no as parent_sale',
                        'T1.id as parent_sale_id',
                        'transactions.status',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }
            if (! empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            $sells->groupBy('transactions.id');
            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'App\Http\Controllers\SellReturnController@showEcom\', [$id])}}"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</a>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('parent_sale', function ($row) {
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]).'">'.$row->parent_sale.'</button>';
                })
                ->addColumn('name', function ($data) {
                    $name= $data->name . ' ' . $data->supplier_business_name;
                    $id = $data->cid??'';
                    return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })                
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;

                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="'.$due.'">'.$due.'</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view')) {
                            return  action([\App\Http\Controllers\SellReturnController::class, 'showEcom'], [$row->id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'action', 'parent_sale' ,'payment_due', 'name'])
                ->make(true);
        }
    }
    public function indexEcomApproved()
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS bl',
                        'transactions.location_id',
                        '=',
                        'bl.id'
                    )
                    ->join(
                        'transactions as T1',
                        'transactions.return_parent_id',
                        '=',
                        'T1.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return_ecom')
                    ->where('transactions.status', 'approved')
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.invoice_no',
                        'contacts.name',
                        'contacts.id as cid',
                        'contacts.supplier_business_name',
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.invoice_no as parent_sale',
                        'T1.id as parent_sale_id',
                        'transactions.status',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }
            if (! empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            $sells->groupBy('transactions.id');
            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="tw-flex tw-gap-5">    
                    <a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'App\Http\Controllers\SellReturnController@showEcom\', [$id])}}"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</a>
                    <a href="#" class="create_picking_btn" data-href="{{action(\'App\Http\Controllers\SellReturnController@manualPickup\', [$id])}}"><i class="fas fa-truck" aria-hidden="true"></i> Create Pickup</a>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('parent_sale', function ($row) {
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]).'">'.$row->parent_sale.'</button>';
                })
                ->addColumn('name', function ($data) {
                    $name= $data->name . ' ' . $data->supplier_business_name;
                    $id = $data->cid??'';
                    return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })                
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;

                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="'.$due.'">'.$due.'</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view')) {
                            return  action([\App\Http\Controllers\SellReturnController::class, 'showEcom'], [$row->id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'action', 'parent_sale' ,'payment_due', 'name'])
                ->make(true);
        }
    }
    public function indexEcomInTransit()
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS bl',
                        'transactions.location_id',
                        '=',
                        'bl.id'
                    )
                    ->join(
                        'transactions as T1',
                        'transactions.return_parent_id',
                        '=',
                        'T1.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return_ecom')
                    ->where('transactions.status', 'in_transit')
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.invoice_no',
                        'contacts.name',
                        'contacts.id as cid',
                        'contacts.supplier_business_name',
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.invoice_no as parent_sale',
                        'T1.id as parent_sale_id',
                        'transactions.status',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }
            if (! empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            $sells->groupBy('transactions.id');
            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'App\Http\Controllers\SellReturnController@showEcom\', [$id])}}"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</a>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('parent_sale', function ($row) {
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]).'">'.$row->parent_sale.'</button>';
                })
                ->addColumn('name', function ($data) {
                    $name= $data->name . ' ' . $data->supplier_business_name;
                    $id = $data->cid??'';
                    return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })                
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;

                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="'.$due.'">'.$due.'</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view')) {
                            return  action([\App\Http\Controllers\SellReturnController::class, 'showEcom'], [$row->id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'action', 'parent_sale' ,'payment_due', 'name'])
                ->make(true);
        }
    }
    public function indexEcomVarified()
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS bl',
                        'transactions.location_id',
                        '=',
                        'bl.id'
                    )
                    ->join(
                        'transactions as T1',
                        'transactions.return_parent_id',
                        '=',
                        'T1.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return_ecom')
                    ->where('transactions.status', 'varified')  
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.invoice_no',
                        'contacts.name',
                        'contacts.id as cid',
                        'contacts.supplier_business_name',
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.invoice_no as parent_sale',
                        'T1.id as parent_sale_id',
                        'transactions.status',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }
            if (! empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            $sells->groupBy('transactions.id');
            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="tw-flex tw-gap-5">    
                    <a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'App\Http\Controllers\SellReturnController@showEcom\', [$id])}}"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</a>
                    <a href="#" class="complete_return_btn" data-id="{{$id}}"><i class="fas fa-truck" aria-hidden="true"></i> Complete Return</a>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('parent_sale', function ($row) {
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]).'">'.$row->parent_sale.'</button>';
                })
                ->addColumn('name', function ($data) {
                    $name= $data->name . ' ' . $data->supplier_business_name;
                    $id = $data->cid??'';
                    return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })                
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;

                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="'.$due.'">'.$due.'</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view')) {
                            return  action([\App\Http\Controllers\SellReturnController::class, 'showEcom'], [$row->id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'action', 'parent_sale' ,'payment_due', 'name'])
                ->make(true);
        }
    }
    public function indexEcomCompleted()
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                    ->join(
                        'business_locations AS bl',
                        'transactions.location_id',
                        '=',
                        'bl.id'
                    )
                    ->join(
                        'transactions as T1',
                        'transactions.return_parent_id',
                        '=',
                        'T1.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return_ecom')
                    ->where('transactions.status', 'completed')
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.invoice_no',
                        'contacts.name',
                        'contacts.id as cid',
                        'contacts.supplier_business_name',
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.invoice_no as parent_sale',
                        'T1.id as parent_sale_id',
                        'transactions.status',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (! empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            // if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            //     $start = request()->start_date;
            //     $end = request()->end_date;
            //     $sells->whereDate('transactions.transaction_date', '>=', $start)
            //             ->whereDate('transactions.transaction_date', '<=', $end);
            // }
            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="btn-group dropdown scroll-safe-dropdown">
                    <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info tw-w-max dropdown-toggle" 
                        data-toggle="dropdown" aria-expanded="false">'.
                        __('messages.actions').
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li><a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'App\Http\Controllers\SellReturnController@showEcom\', [$id])}}"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</a></li>
                    @if($status == "approved")
                        <li><a href="#" class="create_picking_btn" data-href="{{action(\'App\Http\Controllers\SellReturnController@manualPickup\', [$id])}}"><i class="fas fa-truck" aria-hidden="true"></i> Create Pickup</a></li>
                    @endif
                    </ul>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('parent_sale', function ($row) {
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]).'">'.$row->parent_sale.'</button>';
                })
                // ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->addColumn('name', function ($data) {
                    $name= $data->name . ' ' . $data->supplier_business_name;
                    $id = $data->cid??'';
                    return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
                    // return '<a href="#"  class="btn-modal edit-picking-status" data-href="' . action([\App\Http\Controllers\OrderfulfillmentController::class, 'changePickingStatus'], ['id' => $row->id]) . '"><span class="label " style="background-color:'.$color.';">' . $status . '</span></a>';
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })                
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'status',
                    function ($row) {
                        $status =[
                            'approved' => '<span class="label label-warning">Approved</span>',
                            'pending' => '<span class="label label-primary">Pending</span>',
                            'in_transit' => '<span class="label label-info">In Transit</span>',
                            'varified' => '<span class="label label-success">Varified</span>',
                            'completed' => '<span class="label label-success">Completed</span>',
                        ];
                        return $status[$row->status];
                    }
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;

                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="'.$due.'">'.$due.'</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view')) {
                            return  action([\App\Http\Controllers\SellReturnController::class, 'showEcom'], [$row->id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'action', 'parent_sale', 'status', 'payment_due', 'name'])
                ->make(true);
        }
    }

    public function indexEcom()
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, true);

        return view('sell_return.index_ecom')->with(compact('business_locations', 'customers', 'sales_representative'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\SellReturnController::class, 'index']));
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        //$walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        $transactions = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->pluck('invoice_no', 'id');
            

        return view('sell_return.create')
            ->with(compact('business_locations', 'transactions'));
    }

    /**
     * Get the sell data for the return
     *
     * @param int $id
     * @return mixed
     */

    public function getSellData($id){
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $sell = Transaction::where('business_id', $business_id)
                            ->with(['sell_lines', 'location', 'return_parent', 'contact', 'tax', 'sell_lines.sub_unit', 'sell_lines.product', 'sell_lines.product.unit'])
                            ->find($id);
        
        if (!$sell) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        $cid = request()->session()->get('user.id');
        if ($sell->isEditable == false && $sell->editingSalesRep != $cid) {
            $user = User::find($sell->editingSalesRep);
            return response()->json([
                'error' => 'This Order locked by '. $user->first_name . ' ' . $user->last_name
            ], 403);
        } else {
            $sell->isEditable = false;
            $sell->editingSalesRep = $cid;
            $sell->save();
        }

        // Get existing returns for this sale to show return history
        $existing_returns = Transaction::where('business_id', $business_id)
                                    ->where('type', 'sell_return')
                                    ->where('return_parent_id', $sell->id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        // Calculate remaining quantities for each sell line
        foreach ($sell->sell_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }

            // Calculate remaining quantity (original quantity - total returned)
            $total_returned = $value->quantity_returned;
            $remaining_qty = $value->quantity - $total_returned;
            
            $sell->sell_lines[$key]->formatted_qty = $this->transactionUtil->num_f($value->quantity, false, null, true);
            $sell->sell_lines[$key]->remaining_qty = $remaining_qty;
            $sell->sell_lines[$key]->total_returned = $total_returned;
        }

        // Add return history to the response
        $sell->return_history = $existing_returns;

        return response()->json($sell);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response | mixed
     */
    public function add($id)
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        

        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $sell = Transaction::where('business_id', $business_id)
                            ->with(['sell_lines', 'location', 'return_parent', 'contact', 'tax', 'sell_lines.sub_unit', 'sell_lines.product', 'sell_lines.product.unit'])
                            ->find($id);
        // ERP custom disallow for return if the invoice is not paid
        if($sell->payment_status == 'due'){
            return back()->with('status', [
                'success' => 0,
                'msg' => 'For due invoice system not allow to return',
            ]);
        }
        $cid = request()->session()->get('user.id');
        if ($sell->isEditable == false && $sell->editingSalesRep != $cid) {
            $user=  User::find($sell->editingSalesRep);
                return back()->with('status', [
                    'success' => 0,
                    'msg' => 'This Order locked by '. $user->first_name . ' ' . $user->last_name,
            ]);
        } else{
            $sell->isEditable = false;
            $sell->editingSalesRep =  $cid;
            $sell->save();
        }
        foreach ($sell->sell_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }

            $sell->sell_lines[$key]->formatted_qty = $this->transactionUtil->num_f($value->quantity, false, null, true);
        }

        return view('sell_return.add')
            ->with(compact('sell'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->except('_token');

            if (! empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');

                //Check if subscribed or not
                if (! $this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\SellReturnController::class, 'index']));
                }

                $user_id = $request->session()->get('user.id');

                DB::beginTransaction();

                $sell_return = $this->transactionUtil->addSellReturn($input, $business_id, $user_id);

                $receipt = $this->receiptContent($business_id, $sell_return->location_id, $sell_return->id);

                DB::commit();

                $output = ['success' => 1,
                    'msg' => __('lang_v1.success'),
                    'receipt' => $receipt,
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                $msg = __('messages.something_went_wrong');
            }

            $output = ['success' => 0,
                'msg' => $msg,
            ];
        }

        return $output;
    }

    /**
     * Store return invoice request from ecom.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function storeEcomB2B(Request $request)
    {
        try {
            // Validate required fields
            $request->validate([
                'transaction_id' => 'required|integer',
                'customer_email' => 'required|email',
                'return_reason' => 'nullable|string|max:500',
                'additional_notes' => 'nullable|string|max:1000',
                'transaction_date' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max per image
            ]);

            // Check if request is from API (ecom side) or admin side
            $is_api_request = auth('api')->check();
            $auth_contact = null;

            // If request is from API, verify authentication
            if ($is_api_request) {
                $auth_contact = auth('api')->user();
                if (!$auth_contact) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized. Please login to create a return.'
                    ], 401);
                }
            }

         // Use contact's business_id when from API, otherwise default to 1
            $business_id = ($is_api_request && $auth_contact) ? (int) $auth_contact->business_id : 1;
            $user_id = 1; // superadmin user

            // Check if business exists
            $business = \App\Business::find($business_id);
            if (!$business) {
                return response()->json([
                    'status' => false,
                    'message' => 'Business not found'
                ]);
            }

            // Check if user exists
            $user = \App\User::find($user_id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ] );
            }

            $tid = (int) $request->transaction_id;

            // Find the original sale (invoice) transaction
            // 1. First try: transaction_id is the sell/invoice id directly
            $sell = \App\Transaction::where('business_id', $business_id)
                ->where('id', $tid)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->with(['sell_lines', 'contact'])
                ->first();

            // 2. Second try: transaction_id is a sales_order id - find the sell created from it
            if (!$sell) {
                $sell = \App\Transaction::where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->where(function ($q) use ($tid) {
                        $q->whereRaw('JSON_CONTAINS(sales_order_ids, ?)', [json_encode((string) $tid)])
                          ->orWhereRaw('JSON_CONTAINS(sales_order_ids, ?)', [json_encode($tid)]);
                    })
                    ->with(['sell_lines', 'contact'])
                    ->first();
            }

            if (!$sell) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sale transaction not found or not eligible for return'
                ]);
            }

            // Recalculate payment status from transaction_payments (so ERP payments are reflected)
            $this->transactionUtil->updatePaymentStatus($sell->id, $sell->final_total);
            $sell->refresh();

            // Only verify contact_id if request is from API (ecom side)
            if ($is_api_request && $auth_contact) {
                if ($sell->contact_id !== $auth_contact->id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized. You can only create returns for your own orders.'
                    ], 403);
                }
            }

            // Verify customer email matches the sale contact
            if ($sell->contact->email !== $request->customer_email) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer email does not match the sale record'
                ]);
            }

            // Check if invoice is paid (custom ERP rule)
            if ($sell->payment_status == 'due') {
                return response()->json([
                    'status' => false,
                    'message' => 'Returns are not allowed for unpaid invoices'
                ]);
            }

            // Check if any sell return already exists for this sale
            $existing_return = \App\Transaction::where('business_id', $business_id)
                ->where('type', 'sell_return_ecom')
                ->where('return_parent_id', $sell->id)
                ->first();

            if ($existing_return) {
                return response()->json([
                    'status' => false,
                    'message' => 'Return already exists for this sale'
                ]);
            }

            // Check if there are any sell lines to return
            if ($sell->sell_lines->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No products found in this transaction to return'
                ]);
            }

            // Validate that there are returnable items
            $has_returnable_items = false;
            foreach ($sell->sell_lines as $sell_line) {
                $remaining_qty = $sell_line->quantity - $sell_line->quantity_returned;
                if ($remaining_qty > 0) {
                    $has_returnable_items = true;
                    break;
                }
            }

            if (!$has_returnable_items) {
                return response()->json([
                    'status' => false,
                    'message' => 'All items in this transaction have already been returned'
                ]);
            }

            // Calculate return total
            $return_final_total = 0;
            $products_to_return = [];
            
            foreach ($sell->sell_lines as $sell_line) {
                $remaining_qty = $sell_line->quantity - $sell_line->quantity_returned;
                
                // Only add items that have remaining quantity
                if ($remaining_qty > 0) {
                    $return_final_total += $sell_line->unit_price_inc_tax * $remaining_qty;
                    $products_to_return[] = [
                        'sell_line_id' => $sell_line->id,
                        'quantity' => $remaining_qty,
                        'unit_price' => $sell_line->unit_price,
                        'unit_price_inc_tax' => $sell_line->unit_price_inc_tax,
                    ];
                }
            }

            // Handle image uploads
            $return_images = [];
            if ($request->hasFile('images')) {
                $upload_path = public_path('uploads/return_images');
                if (!file_exists($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    if ($image->move($upload_path, $imageName)) {
                        $return_images[] = 'return_images/' . $imageName;
                    }
                }
            }

            // Begin transaction
            DB::beginTransaction();

            try {
                $ref_count = $this->transactionUtil->setAndGetReferenceCount('sell_return', $business_id);
                $newValue = $this->transactionUtil->generateReferenceNumber('sell_return', $ref_count, $business_id);
                $data = [
                    'business_id' => $business_id,
                    'type' => 'sell_return_ecom',
                    'return_parent_id' => $sell->id,
                    'contact_id' => $sell->contact_id,
                    'location_id' => $sell->location_id,
                    'invoice_no' => $newValue,
                    'final_total' => $return_final_total,
                    'payment_status' => $sell->payment_status,
                    'status' => 'pending',
                    'created_by' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'additional_notes' => $request->additional_notes,
                    'is_return' => 1,
                    'shipping_firstname' => $sell->shipping_firstname,
                    'shipping_lastname' => $sell->shipping_lastname,
                    'shipping_company' => $sell->shipping_company,
                    'shipping_address1' => $sell->shipping_address1,
                    'shipping_address2' => $sell->shipping_address2,
                    'shipping_city' => $sell->shipping_city,
                    'shipping_state' => $sell->shipping_state,
                    'shipping_zip' => $sell->shipping_zip,
                    'shipping_country' => $sell->shipping_country,
                ];
                
                // Store images as JSON in document field
                if (!empty($return_images)) {
                    $data['document'] = json_encode($return_images);
                }
                
                $sell_return = Transaction::create($data);

                // Create return lines for all returnable products
                $return_lines_data = [];
                foreach ($products_to_return as $product) {
                    $sell_line = $sell->sell_lines->where('id', $product['sell_line_id'])->first();
                    $return_lines_data[] = [
                        'transaction_id' => $sell_return->id,
                        'product_id' => $sell_line->product_id,
                        'variation_id' => $sell_line->variation_id,
                        'quantity' => $product['quantity'],
                        'return_price' => $product['unit_price_inc_tax'],
                        'parent_sell_line_id' => $sell_line->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                TransactionReturnEcom::insert($return_lines_data);

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Return created successfully for all items in the transaction',
                    'return_id' => $sell_return->id,
                    'return_invoice_no' => $sell_return->invoice_no,
                    'return_amount' => $sell_return->final_total,
                    'items_returned' => count($products_to_return),
                    'images_uploaded' => count($return_images),
                    'created_at' => $sell_return->created_at
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                // throw $e;
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong while processing the return request',
                    'errors' => $e->getMessage() . ' at ' . $e->getFile() . ' at ' . $e->getLine()
                ]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ]);

        } catch (\App\Exceptions\PurchaseSellMismatch $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while processing the return request',
                'errors' => $e->getMessage() . ' at ' . $e->getFile() . ' at ' . $e->getLine()
            ]);
        }
    }
    public function storeEcom(Request $request)
    {
        $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
        if (!in_array('sales_returns', $enabled_modules)) {
            return response()->json([
                'status' => false,
                'message' => 'Sales returns is Not Allowed'
            ]);
        }
        try {
            // Validate required fields
            $request->validate([
                'invoice_no' => 'required|string',
                'customer_email' => 'required|email',
                'products' => 'required|array|min:1',
                'products.*.sell_line_id' => 'required|integer',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_price' => 'required|numeric|min:0',
                'products.*.unit_price_inc_tax' => 'nullable|numeric|min:0',
                'return_reason' => 'nullable|string|max:500',
                'additional_notes' => 'nullable|string|max:1000',
                'transaction_date' => 'nullable|string',
                'transaction_id' => 'nullable|integer',
                'location_id' => 'nullable|integer',
            ]);

            // Check if request is from API (ecom side) or admin side
            $is_api_request = auth('api')->check();
            $auth_contact = null;

            // If request is from API, verify authentication
            if ($is_api_request) {
                $auth_contact = auth('api')->user();
                if (!$auth_contact) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized. Please login to create a return.'
                    ], 401);
                }
            }

            // Hardcoded values for stateless operation
            $business_id = 1;
            $user_id = 1; // superadmin user

            // Check if business exists
            $business = \App\Business::find($business_id);
            if (!$business) {
                return response()->json([
                    'status' => false,
                    'message' => 'Business not found'
                ]);
            }

            // Check if user exists
            $user = \App\User::find($user_id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ] );
            }

            // Find the original sale transaction
            $sell = \App\Transaction::where('business_id', $business_id)
                ->where('invoice_no', $request->invoice_no)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->with(['sell_lines', 'contact'])
                ->first();

            if (!$sell) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sale transaction not found or not eligible for return'
                ]);
            }

            // Only verify contact_id if request is from API (ecom side)
            if ($is_api_request && $auth_contact) {
                if ($sell->contact_id !== $auth_contact->id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized. You can only create returns for your own orders.'
                    ], 403);
                }
            }

            // Verify customer email matches the sale contact
            if ($sell->contact->email !== $request->customer_email) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer email does not match the sale record'
                ]);
            }

            // Check if invoice is paid (custom ERP rule)
            if ($sell->payment_status == 'due') {
                return response()->json([
                    'status' => false,
                    'message' => 'Returns are not allowed for unpaid invoices'
                ]);
            }

            // Check if any sell return already exists for this sale
            $existing_return = \App\Transaction::where('business_id', $business_id)
                ->where('type', 'sell_return_ecom')
                ->where('return_parent_id', $sell->id)
                ->first();

            if ($existing_return) {
                return response()->json([
                    'status' => false,
                    'message' => 'Return already exists for this sale'
                ]);
            }

            // Validate return quantities
            foreach ($request->products as $product) {
                $sell_line = $sell->sell_lines->where('id', $product['sell_line_id'])->first();
                
                if (!$sell_line) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid sell line ID: ' . $product['sell_line_id']
                    ]);
                }

                $remaining_qty = $sell_line->quantity - $sell_line->quantity_returned;
                
                // Check if there are any remaining items to return
                if ($remaining_qty <= 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No remaining items to return for product: ' . $sell_line->product->name . ' (Already returned: ' . $sell_line->quantity_returned . ')'
                    ]);
                }
                
                if ($product['quantity'] > $remaining_qty) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Return quantity exceeds remaining quantity for product: ' . $sell_line->product->name . ' (Requested: ' . $product['quantity'] . ', Remaining: ' . $remaining_qty . ')'
                    ]);
                }

                // Validate return price doesn't exceed original purchase price
                $original_unit_price = (float) $sell_line->unit_price;
                $return_unit_price = (float) $product['unit_price'];
                
                if ($return_unit_price > $original_unit_price) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Return unit price cannot exceed original purchase price for product: ' . $sell_line->product->name . ' (Return price: ' . $return_unit_price . ', Original price: ' . $original_unit_price . ')'
                    ]);
                }

                // Validate unit_price_inc_tax if provided
                if (isset($product['unit_price_inc_tax'])) {
                    $return_unit_price_inc_tax = (float) $product['unit_price_inc_tax'];
                    $original_unit_price_inc_tax = (float) $sell_line->unit_price_inc_tax;
                    
                    if ($return_unit_price_inc_tax > $original_unit_price_inc_tax) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Return unit price (inc. tax) cannot exceed original purchase price (inc. tax) for product: ' . $sell_line->product->name . ' (Return price: ' . $return_unit_price_inc_tax . ', Original price: ' . $original_unit_price_inc_tax . ')'
                        ]);
                    }
                }
            }

            // Prepare input data for addSellReturn
            $input = [
                'transaction_id' => $sell->id,
                'products' => $request->products,
                'return_reason' => $request->return_reason,
                'additional_notes' => $request->additional_notes,
                'discount_type' => 'fixed',
                'discount_amount' => 0,
                'tax_id' => $sell->tax_id,
            ];

            // Handle transaction date properly
            if ($request->filled('transaction_date')) {
                try {
                    // Parse the date string and format it properly
                    $parsed_date = \Carbon\Carbon::createFromFormat('m/d/Y H:i', $request->transaction_date);
                    $input['transaction_date'] = $parsed_date->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    // If date parsing fails, use current date
                    $input['transaction_date'] = now()->format('Y-m-d H:i:s');
                }
            } else {
                $input['transaction_date'] = now()->format('Y-m-d H:i:s');
            }

            $return_final_total = 0;
            foreach ($request->products as $product) {
                $return_final_total += $product['unit_price_inc_tax'] * $product['quantity'];
            }

            // Begin transaction
            DB::beginTransaction();

            try {
                $ref_count = $this->transactionUtil->setAndGetReferenceCount('sell_return', $business_id);
                $newValue = $this->transactionUtil->generateReferenceNumber('sell_return', $ref_count, $business_id);
                $data = [
                    'business_id' => $business_id,
                    'type' => 'sell_return_ecom',
                    'return_parent_id' => $sell->id,
                    'contact_id' => $sell->contact_id,
                    'location_id' => $sell->location_id,
                    'invoice_no' => $newValue,
                    'final_total' => $return_final_total,
                    'payment_status' => $sell->payment_status,
                    'status' => 'pending',
                    'created_by' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'additional_notes' => $request->additional_notes,
                    'is_return' => 1,
                    'shipping_firstname' => $sell->shipping_firstname,
                    'shipping_lastname' => $sell->shipping_lastname,
                    'shipping_company' => $sell->shipping_company,
                    'shipping_address1' => $sell->shipping_address1,
                    'shipping_address2' => $sell->shipping_address2,
                    'shipping_city' => $sell->shipping_city,
                    'shipping_state' => $sell->shipping_state,
                    'shipping_zip' => $sell->shipping_zip,
                    'shipping_country' => $sell->shipping_country,
                ];
                $sell_return = Transaction::create($data);

                $data = [];
                foreach ($request->products as $product) {
                    // "products": [
                    //     {
                    //         "sell_line_id": 360,
                    //         "quantity": 1,
                    //         "unit_price": 10.00,
                    //         "unit_price_inc_tax": 10.0000
                    //     }
                    // ]
                    $sell_line = $sell->sell_lines->where('id', $product['sell_line_id'])->first();
                    $data[] = [
                        'transaction_id' => $sell_return->id,
                        'product_id' => $sell_line->product_id,
                        'variation_id' => $sell_line->variation_id,
                        'quantity' => $product['quantity'],
                        'return_price' => $product['unit_price_inc_tax'],
                        'parent_sell_line_id' => $sell_line->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                TransactionReturnEcom::insert($data);

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Return created successfully',
                    'return_id' => $sell_return->id,
                    'return_invoice_no' => $sell_return->invoice_no,
                    'return_amount' => $sell_return->final_total,
                    'created_at' => $sell_return->created_at
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                // throw $e;
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong while processing the return request',
                    'errors' => $e->getMessage() . ' at ' . $e->getFile() . ' at ' . $e->getLine()
                ]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ]);

        } catch (\App\Exceptions\PurchaseSellMismatch $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while processing the return request',
                'errors' => $e->getMessage() . ' at ' . $e->getFile() . ' at ' . $e->getLine()
            ]);
        }
    }

    public function storeSellReturn($id)
    {
        $return = Transaction::find($id)->where('type', 'sell_return_ecom')->first();
        $return_lines = TransactionReturnEcom::where('transaction_id', $return->id)->get();

        $contact = Contact::find($return->contact_id);

        $request = [
            'invoice_no' => $return->invoice_no,
            'customer_email' => $contact->email,
            'transaction_date' => $return->created_at,
            'transaction_id' => $return->return_parent_id,
            'location_id' => $return->location_id ?? 1,
            'additional_notes' => $return->additional_notes,
        ];

        $products = [];
        foreach ($return_lines as $line) {
            $products[] = [
                'sell_line_id' => $line->parent_sell_line_id,
                'quantity' => $line->quantity,
                'unit_price' => $line->return_price,
                'unit_price_inc_tax' => $line->return_price,
            ];
        }
        $request['products'] = $products;

        try {
            // Hardcoded values for stateless operation
            $business_id = 1;
            $user_id = 1; // superadmin user

            // Check if business exists
            $business = \App\Business::find($business_id);
            if (!$business) {
                return response()->json([
                    'status' => false,
                    'message' => 'Business not found'
                ]);
            }

            // Check if user exists
            $user = \App\User::find($user_id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ] );
            }

            // Find the original sale transaction
            $sell = \App\Transaction::where('business_id', $business_id)
                ->where('id', $return->return_parent_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->with(['sell_lines', 'contact'])
                ->first();

            if (!$sell) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sale transaction not found or not eligible for return'
                ]);
            }

            // Verify customer email matches the sale contact
            if ($sell->contact->email !== $request['customer_email']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer email does not match the sale record'
                ]);
            }

            // Check if invoice is paid (custom ERP rule)
            if ($sell->payment_status == 'due') {
                return response()->json([
                    'status' => false,
                    'message' => 'Returns are not allowed for unpaid invoices'
                ]);
            }

            // Check if any sell return already exists for this sale
            $existing_return = \App\Transaction::where('business_id', $business_id)
                ->where('type', 'sell_return')
                ->where('return_parent_id', $sell->id)
                ->first();

            if ($existing_return) {
                return response()->json([
                    'status' => false,
                    'message' => 'Return already exists for this sale'
                ]);
            }

            // Validate return quantities
            foreach ($request['products'] as $product) {
                $sell_line = $sell->sell_lines->where('id', $product['sell_line_id'])->first();
                
                if (!$sell_line) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid sell line ID: ' . $product['sell_line_id']
                    ]);
                }

                $remaining_qty = $sell_line->quantity - $sell_line->quantity_returned;
                
                // Check if there are any remaining items to return
                if ($remaining_qty <= 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No remaining items to return for product: ' . $sell_line->product->name . ' (Already returned: ' . $sell_line->quantity_returned . ')'
                    ]);
                }
                
                if ($product['quantity'] > $remaining_qty) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Return quantity exceeds remaining quantity for product: ' . $sell_line->product->name . ' (Requested: ' . $product['quantity'] . ', Remaining: ' . $remaining_qty . ')'
                    ]);
                }

                // Validate return price doesn't exceed original purchase price
                $original_unit_price = (float) $sell_line->unit_price;
                $return_unit_price = (float) $product['unit_price'];
                
                if ($return_unit_price > $original_unit_price) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Return unit price cannot exceed original purchase price for product: ' . $sell_line->product->name . ' (Return price: ' . $return_unit_price . ', Original price: ' . $original_unit_price . ')'
                    ]);
                }

                // Validate unit_price_inc_tax if provided
                if (isset($product['unit_price_inc_tax'])) {
                    $return_unit_price_inc_tax = (float) $product['unit_price_inc_tax'];
                    $original_unit_price_inc_tax = (float) $sell_line->unit_price_inc_tax;
                    
                    if ($return_unit_price_inc_tax > $original_unit_price_inc_tax) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Return unit price (inc. tax) cannot exceed original purchase price (inc. tax) for product: ' . $sell_line->product->name . ' (Return price: ' . $return_unit_price_inc_tax . ', Original price: ' . $original_unit_price_inc_tax . ')'
                        ]);
                    }
                }
            }

            // Prepare input data for addSellReturn
            $input = [
                'transaction_id' => $sell->id,
                'products' => $request['products'],
                'return_reason' => $request['return_reason'] ?? null,
                'additional_notes' => $request['additional_notes'] ?? null,
                'discount_type' => 'fixed',
                'discount_amount' => 0,
                'tax_id' => $sell->tax_id,
            ];

            // Handle transaction date properly
            if (isset($request['transaction_date'])) {
                try {
                    // Parse the date string and format it properly
                    $parsed_date = \Carbon\Carbon::createFromFormat('m/d/Y H:i', $request['transaction_date']);
                    $input['transaction_date'] = $parsed_date->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    // If date parsing fails, use current date
                    $input['transaction_date'] = now()->format('Y-m-d H:i:s');
                }
            } else {
                $input['transaction_date'] = now()->format('Y-m-d H:i:s');
            }

            // Begin transaction
            DB::beginTransaction();

            try {
                // Create the sell return using existing utility method
                // Pass uf_number = false to avoid date parsing issues in stateless context
                $sell_return = $this->transactionUtil->addSellReturn($input, $business_id, $user_id, false);

                // Add return reason and notes if provided
                $sell_return->additional_notes = $request['additional_notes'] ?? null;
                $sell_return->save();
                
                $return->status = 'completed';
                $return->save();
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Return created successfully',
                    'return_id' => $sell_return->id,
                    'return_invoice_no' => $sell_return->invoice_no,
                    'return_amount' => $sell_return->final_total,
                    'created_at' => $sell_return->created_at
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                // throw $e;
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong while processing the return request',
                    'errors' => $e->getMessage() . ' at ' . $e->getFile() . ' at ' . $e->getLine()
                ]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ]);

        } catch (\App\Exceptions\PurchaseSellMismatch $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while processing the return request',
                'errors' => $e->getMessage() . ' at ' . $e->getFile() . ' at ' . $e->getLine()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $query = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'return_parent',
                                    'tax',
                                    'sell_lines',
                                    'sell_lines.product',
                                    'sell_lines.variations',
                                    'sell_lines.sub_unit',
                                    'sell_lines.product',
                                    'sell_lines.product.unit',
                                    'location'
                                );

        if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
            $sells->where('created_by', request()->session()->get('user.id'));
        }
        $sell = $query->first();

        if (!$sell) {
            abort(404, 'Sell return not found.');
        }

        foreach ($sell->sell_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
        }

        $sell_taxes = [];
        if (! empty($sell->return_parent->tax)) {
            if ($sell->return_parent->tax->is_tax_group) {
                $sell_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->return_parent->tax, $sell->return_parent->tax_amount));
            } else {
                $sell_taxes[$sell->return_parent->tax->name] = $sell->return_parent->tax_amount;
            }
        }

        $total_discount = 0;
        if ($sell->return_parent->discount_type == 'fixed') {
            $total_discount = $sell->return_parent->discount_amount;
        } elseif ($sell->return_parent->discount_type == 'percentage') {
            $discount_percent = $sell->return_parent->discount_amount;
            if ($discount_percent == 100) {
                $total_discount = $sell->return_parent->total_before_tax;
            } else {
                $total_after_discount = $sell->return_parent->final_total - $sell->return_parent->tax_amount;
                $total_before_discount = $total_after_discount * 100 / (100 - $discount_percent);
                $total_discount = $total_before_discount - $total_after_discount;
            }
        }

        $activities = Activity::forSubject($sell->return_parent)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        return view('sell_return.show')
            ->with(compact('sell', 'sell_taxes', 'total_discount', 'activities'));
    }

    /**
     * Display the specified resource for ecom.
     *
     * @param  int  $id
     * @return mixed
     */
    public function showEcom($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $return = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->where('type', 'sell_return_ecom')
            ->with(['return_lines_ecom', 'return_lines_ecom.product', 'return_lines_ecom.variations', 'location','return_lines_ecom.parent_sell_line'])
            ->first();
        return view('sell_return.show_ecom')
            ->with(compact('return'));
    }
    public function varifyEcom($id)
    {
        $products = request()->products;
        $business_id = request()->session()->get('user.business_id');
        $return = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->where('type', 'sell_return_ecom')
            ->with(['return_lines_ecom', 'return_lines_ecom.product', 'return_lines_ecom.variations', 'location','return_lines_ecom.parent_sell_line'])
            ->first();      
        if (!$return) {
            return response()->json([
                'status' => false,
                'message' => 'Return not found'
            ]);
        }
        if($return->status == 'pending'){
            $return_final_total = 0;
        DB::beginTransaction();
        $return_lines = $return->return_lines_ecom;
        foreach ($products as $product) {
            $return_line = $return_lines->where('id', $product['return_line_id'])->first();
            $return_line->quantity = $product['quantity'];
            $return_line->return_price = $product['unit_price_inc_tax'];
            $return_line->save();
            $return_final_total += $product['unit_price_inc_tax'] * $product['quantity'];
        }
        $return->final_total = $return_final_total;
        $return->status = 'approved';
        $return->save();
        DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'Return approved successfully',
        ]);
        }else{
            $return_final_total = 0;
            DB::beginTransaction();
            $return_lines = $return->return_lines_ecom;
            foreach ($products as $product) {
            $return_line = $return_lines->where('id', $product['return_line_id'])->first();
            $return_line->quantity = $product['quantity'];
            $return_line->return_price = $product['unit_price_inc_tax'];
            $return_line->save();
            $return_final_total += $product['unit_price_inc_tax'] * $product['quantity'];
           }
           $return->final_total = $return_final_total;
           $return->status = 'varified';
           $return->shipping_status = 'Received';
           $return->save();
           DB::commit();
           return response()->json([
            'status' => true,
            'message' => 'Return verified successfully',
           ]);
        };
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                //Begin transaction
                DB::beginTransaction();

                $query = Transaction::where('id', $id)
                    ->where('business_id', $business_id)
                    ->where('type', 'sell_return')
                    ->with(['sell_lines', 'payment_lines']);

                if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
                    $sells->where('created_by', request()->session()->get('user.id'));
                }
                $sell_return = $query->first();

                $sell_lines = TransactionSellLine::where('transaction_id',
                                            $sell_return->return_parent_id)
                                    ->get();

                if (! empty($sell_return)) {
                    $transaction_payments = $sell_return->payment_lines;

                    foreach ($sell_lines as $sell_line) {
                        if ($sell_line->quantity_returned > 0) {
                            $quantity = 0;
                            $quantity_before = $this->transactionUtil->num_f($sell_line->quantity_returned);

                            $sell_line->quantity_returned = 0;
                            $sell_line->save();

                            //update quantity sold in corresponding purchase lines
                            $this->transactionUtil->updateQuantitySoldFromSellLine($sell_line, 0, $quantity_before);

                            // Update quantity in variation location details
                            $this->productUtil->updateProductQuantity($sell_return->location_id, $sell_line->product_id, $sell_line->variation_id, 0, $quantity_before);
                        }
                    }

                    $sell_return->delete();
                    foreach ($transaction_payments as $payment) {
                        event(new TransactionPaymentDeleted($payment));
                    }
                }

                DB::commit();
                $output = ['success' => 1,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                DB::rollBack();

                if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                    $msg = $e->getMessage();
                } else {
                    \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                    $msg = __('messages.something_went_wrong');
                }

                $output = ['success' => 0,
                    'msg' => $msg,
                ];
            }

            return $output;
        }
    }

    /**
     * Returns the content for the receipt
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  int  $transaction_id
     * @param  string  $printer_type = null
     * @return array
     */
    private function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null
    ) {
        $output = ['is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => [],
        ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        //Check if printing of invoice is enabled or not.
        if ($location_details->print_receipt_on_invoice == 1) {
            //If enabled, get print type.
            $output['is_enabled'] = true;

            $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_details->invoice_layout_id);

            //Check if printer setting is provided.
            $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

            $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

            //If print type browser - return the content, printer - return printer config data, and invoice format config
            $output['print_title'] = $receipt_details->invoice_no;
            if ($receipt_printer_type == 'printer') {
                $output['print_type'] = 'printer';
                $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
                $output['data'] = $receipt_details;
            } else {
                $output['html_content'] = view('sell_return.receipt', compact('receipt_details'))->render();
            }
        }

        return $output;
    }

    /**
     * Prints invoice for sell
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice(Request $request, $transaction_id)
    {
        if (request()->ajax()) {
            try {
                $output = ['success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];

                $business_id = $request->session()->get('user.business_id');

                $transaction = Transaction::where('business_id', $business_id)
                                ->where('id', $transaction_id)
                                ->first();

                if (empty($transaction)) {
                    return $output;
                }

                $receipt = $this->receiptContent($business_id, $transaction->location_id, $transaction_id, 'browser');

                if (! empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                $output = ['success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Function to validate sell for sell return
     */
    public function validateInvoiceToReturn($invoice_no)
    {
        if (! auth()->user()->can('sell.create') && ! auth()->user()->can('direct_sell.access') && ! auth()->user()->can('view_own_sell_only')) {
            return ['success' => 0,
                'msg' => trans('lang_v1.permission_denied'),
            ];
        }

        $business_id = request()->session()->get('user.business_id');
        $query = Transaction::where('business_id', $business_id)
                            ->where('invoice_no', $invoice_no);

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        if (! auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
            $query->where('created_by', auth()->user()->id);
        }

        $sell = $query->first();

        if (empty($sell)) {
            return ['success' => 0,
                'msg' => trans('lang_v1.sell_not_found'),
            ];
        }

        return ['success' => 1,
            'redirect_url' => action([\App\Http\Controllers\SellReturnController::class, 'add'], [$sell->id]),
        ];
    }

    public function manualPickup($id)
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access') && !auth()->user()->can('view_own_sell_only')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
            ->pluck('name', 'id');
        try {
            $packingOrder = Transaction::with(['sell_lines' => function ($query) {
                $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'picked_quantity', 'ordered_quantity');
            }])
                // ->where('pickerID', request()->session()->get('user.id'))
                ->where('type', 'sell_return_ecom')
                ->where('id', $id)
                ->first();

            if (!$packingOrder) {
                return response()->json(['status' => false, 'message' => 'Picking order not found']);
            }
            $user = Contact::where('id', $packingOrder->contact_id)->first();
            $shipstation = ShipStation::where('usable', 1)->orderBy('priority', 'desc')->get();
            return view('sell_return.partials.pickup_return')
                ->with(compact(
                    'packingOrder',
                    'user',
                    'shipstation'
                ));
            // return response()->json(['status' => true, 'order_details' => $orderDetails]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

       public function sellsInvoiceReturnStore(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $id = (int) $request->input('sale_invoice_no');
        $query = Transaction::with(['contact', 'sell_lines'])->where('business_id', $business_id)
            ->where('id', $id)->first();
        $final_total = $query->final_total;
        $totalBeforeTax = $query->total_before_tax;
        if ($query->type == 'sell_return') {
            return response()->json(['status' => false, 'msg' => "Sell Return already created"]);
        }
        try {
            $shipingCharges = 0;
            $shipping_label = '';
            $shipment = $request->input('shipment');

             if (is_array($shipment) && isset($shipment['shipment_type']) && $shipment['shipment_type'] == 'own'){
                return response()->json(['status' => false, 'msg' => "Shipment type Currently Not Available"]);
            }else if (is_array($shipment) && isset($shipment['shipment_type']) && $shipment['shipment_type'] == 'manual'){
                $shipmentData = $request->shipment;
                $shipingCharges = $shipmentData['shipping_charges'] ?? 0;
                $now = now(); 
                $trackingNumber = $now->format('YmdHis');
                $app_url=config('app.url');
                $shipment_labal_url=$app_url.'/download-shipment-label/'.$id.'/pdf';
                $shipmentDetails = [
                    'warehouse_id' => $request->warehouse_id,
                    'shipment_cost' => [
                        'currency' => 'usd',
                        'amount' => $shipmentData['shipping_charges'] ?? 0
                    ],
                    'carrier_id' => $shipmentData['ship_from']['company_name'] . " Delivery" ?? null,
                    'service_code' => "Manual Shipment",
                    'package_code' => $shipmentData['package_code'] ?? null,
                    'label_download' => $shipment_labal_url ?? [],
                    'tracking_number' =>  $trackingNumber,
                    'tracking_url' => null,
                    'carrier_name' => $shipmentData['ship_from']['company_name'] ?? null,
                    "delivery_date" => $request->date ?? null,
                ];
                $shipping_label = $shipment_labal_url ?? [];
                $packages = [];
                if (!empty($shipmentData['packages']) && is_array($shipmentData['packages'])) {
                    foreach ($shipmentData['packages'] as $package) {
                        $packages[] = [
                            'package_id' => $package['package_id'] ?? null,
                            'package_code' => $package['package_code'] ?? null,
                            'weight' => $package['weight'] ?? [],
                            'dimensions' => $package['dimensions'] ?? [],
                            'insured_value' => $package['insured_value'] ?? [],
                            'tracking_number' => $package['tracking_number'] ?? null,
                            'label_download' => $package['label_download'] ?? []
                        ];
                    }
                }
                $shipmentPayload = [
                    'shipment_details' => $shipmentDetails,
                    'packages' => $packages
                ];
                $shipmentJson = json_encode($shipmentPayload);
                DB::table('transactions')->where('id', $id)->update([
                    'shipment' => $shipmentJson,
                    'shipping_status' => 'Pickup Requested',
                    'shipping_details' => $shipmentData['tracking_number'] ?? null,
                ]);
            }

            DB::table('transactions')->where('id', $id)->update([
                'final_total' => $final_total + $shipingCharges,
                'shipping_charges' => $shipingCharges,
                'shipping_status' => 'pickup_requested',
                'status' => 'in_transit',
                'total_before_tax' => $totalBeforeTax,
                'updated_at' => now(),
            ]);
            DB::commit();
            if ($request->input('shipment')) {

                return response()->json(['status' => true, 'msg' => "pickup request sent", "shipping_label" => $shipping_label, 'transaction' => $id]);
            } else {
                return response()->json(['status' => true, 'msg' => "pickup request sent", "shipping_label" => $shipping_label, 'transaction' => $id]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'An error occurred while processing the request: ' . $e->getMessage() . ' ' . $e->getLine()]);
        }
    }
}
