<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use Illuminate\Support\Facades\Auth;
use App\Events\TransactionPaymentAdded;
use App\InvoiceScheme;
use App\Jobs\SendNotificationJob;
use App\Media;
use App\Product;
use App\PurchaseLine;
use App\ReferenceCount;
use App\SellingPriceGroup;
use App\ShipStation;
use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\TypesOfService;
use App\Variation;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\VariationLocationDetails;
use App\Warranty;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\NotificationUtil;
use Tymon\JWTAuth\Facades\JWTAuth;

class SellController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $contactUtil;

    protected $businessUtil;

    protected $transactionUtil;

    protected $productUtil;

    protected $notificationUtil;


    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, NotificationUtil $notificationUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->notificationUtil = $notificationUtil;


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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $is_crm = $this->moduleUtil->isModuleInstalled('Crm');
        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');
        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');

        if (request()->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $sale_type = ! empty(request()->input('sale_type')) ? request()->input('sale_type') : 'sell';

            $sells = $this->transactionUtil->getListSells($business_id, $sale_type);

            // only display sell invoice we add it because project invoive show in sell list
            if ($sale_type == 'sell') {
                $sells->whereNull('transactions.sub_type');
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }

            $only_shipments = request()->only_shipments == 'true' ? true : false;
            if ($only_shipments) {
                $sells->whereNotNull('transactions.shipping_status');

                if (auth()->user()->hasAnyPermission(['access_pending_shipments_only'])) {
                    $sells->where('transactions.shipping_status', '!=', 'delivered');
                }
            }

            if (! $is_admin && ! $only_shipments && $sale_type != 'sales_order') {
                $payment_status_arr = [];
                if (auth()->user()->can('view_paid_sells_only')) {
                    $payment_status_arr[] = 'paid';
                }

                if (auth()->user()->can('view_due_sells_only')) {
                    $payment_status_arr[] = 'due';
                }

                if (auth()->user()->can('view_partial_sells_only')) {
                    $payment_status_arr[] = 'partial';
                }

                if (empty($payment_status_arr)) {
                    if (auth()->user()->can('view_overdue_sells_only')) {
                        $sells->OverDue();
                    }
                } else {
                    if (auth()->user()->can('view_overdue_sells_only')) {
                        $sells->where(function ($q) use ($payment_status_arr) {
                            $q->whereIn('transactions.payment_status', $payment_status_arr)
                                ->orWhere(function ($qr) {
                                    $qr->OverDue();
                                });
                        });
                    } else {
                        $sells->whereIn('transactions.payment_status', $payment_status_arr);
                    }
                }
            }

            if (! empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $sells->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $sells->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (! empty(request()->input('rewards_only')) && request()->input('rewards_only') == true) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.rp_earned')
                        ->orWhere('transactions.rp_redeemed', '>', 0);
                });
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

            //Check is_direct sell
            if (request()->has('is_direct_sale')) {
                $is_direct_sale = request()->is_direct_sale;
                if ($is_direct_sale == 0) {
                    $sells->where('transactions.is_direct_sale', 0);
                    $sells->whereNull('transactions.sub_type');
                }
            }

            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (! empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }

            if (! empty(request()->input('source'))) {
                //only exception for woocommerce
                if (request()->input('source') == 'woocommerce') {
                    $sells->whereNotNull('transactions.woocommerce_order_id');
                } else {
                    $sells->where('transactions.source', request()->input('source'));
                }
            }

            if ($is_crm) {
                $sells->addSelect('transactions.crm_is_order_request');

                if (request()->has('crm_is_order_request')) {
                    $sells->where('transactions.crm_is_order_request', 1);
                }
            }

            if (request()->only_subscriptions) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.recur_parent_id')
                        ->orWhere('transactions.is_recurring', 1);
                });
            }

            if (! empty(request()->list_for) && request()->list_for == 'service_staff_report') {
                $sells->whereNotNull('transactions.res_waiter_id');
            }

            if (! empty(request()->res_waiter_id)) {
                $sells->where('transactions.res_waiter_id', request()->res_waiter_id);
            }

            if (! empty(request()->input('sub_type'))) {
                $sells->where('transactions.sub_type', request()->input('sub_type'));
            }

            if (! empty(request()->input('created_by'))) {
                $sells->where('transactions.created_by', request()->input('created_by'));
            }

            if (! empty(request()->input('status'))) {
                $sells->where('transactions.status', request()->input('status'));
            }

            if (! empty(request()->input('sales_cmsn_agnt'))) {
                $sells->where('transactions.commission_agent', request()->input('sales_cmsn_agnt'));
            }

            if (! empty(request()->input('service_staffs'))) {
                $sells->where('transactions.res_waiter_id', request()->input('service_staffs'));
            }

            $only_pending_shipments = request()->only_pending_shipments == 'true' ? true : false;
            if ($only_pending_shipments) {
                $sells->where('transactions.shipping_status', '!=', 'delivered')
                    ->whereNotNull('transactions.shipping_status');
                $only_shipments = true;
            }

            if (! empty(request()->input('shipping_status'))) {
                $sells->where('transactions.shipping_status', request()->input('shipping_status'));
            }

            if (! empty(request()->input('for_dashboard_sales_order'))) {
                $sells->whereIn('transactions.status', ['partial', 'ordered'])
                    ->orHavingRaw('so_qty_remaining > 0');
            }

            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            if (! empty(request()->input('delivery_person'))) {
                $sells->where('transactions.delivery_person', request()->input('delivery_person'));
            }

            $sells->groupBy('transactions.id');

            if (! empty(request()->suspended)) {
                $transaction_sub_type = request()->get('transaction_sub_type');
                if (! empty($transaction_sub_type)) {
                    $sells->where('transactions.sub_type', $transaction_sub_type);
                } else {
                    $sells->where('transactions.sub_type', null);
                }

                $with = ['sell_lines'];

                if ($is_tables_enabled) {
                    $with[] = 'table';
                }

                if ($is_service_staff_enabled) {
                    $with[] = 'service_staff';
                }

                $sales = $sells->where('transactions.is_suspend', 1)
                    ->with($with)
                    ->addSelect('transactions.is_suspend', 'transactions.res_table_id', 'transactions.res_waiter_id', 'transactions.additional_notes')
                    ->get();

                return view('sale_pos.partials.suspended_sales_modal')->with(compact('sales', 'is_tables_enabled', 'is_service_staff_enabled', 'transaction_sub_type'));
            }

            $with[] = 'payment_lines';

            if (!empty($with)) {
                foreach ($with as $relation) {
                    if ($relation == 'payment_lines' && !empty(request()->input('payment_method'))) {
                        $sells->whereHas($relation, function ($query) {
                            $query->where('method', request()->input('payment_method'));
                        });
                    } else {
                        $sells->with($relation);
                    }
                }
            }

            //$business_details = $this->businessUtil->getDetails($business_id);
            if ($this->businessUtil->isModuleEnabled('subscription')) {
                $sells->addSelect('transactions.is_recurring', 'transactions.recur_parent_id');
            }
            if ($sale_type == 'sales_order') {
                if (auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->addSelect('transactions.picking_status');
                }
            }

            $sales_order_statuses = Transaction::sales_order_statuses();
            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) use ($only_shipments, $is_admin, $sale_type) {
                        if ($row->status == 'void') {
                            return '';
                        }
                        $html = '<div class="btn-group dropdown scroll-safe-dropdown
                        ">
                        <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline ' .
                            ($row->picking_status == 'INVOICED' ? 'tw-dw-btn-success' : 'tw-dw-btn-info') .
                            ' tw-w-max dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' .
                            __('messages.actions') .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">';


                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
                            $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i> ' . __('messages.view') . '</a></li>';
                        }
                        $cid_url="";
                        if (! empty(request()->customer_id)) {
                         $cid_url='cid='.request()->customer_id;
                        }
                        if (! $only_shipments) {
                            // Check sales_order type FIRST before is_direct_sale
                            if ($row->type == 'sales_order') {
                                // Allow editing if: user has permission, status is not void/cancelled, and picking_status is not INVOICED
                                // Note: Orders with NULL picking_status or PICKING status should be editable
                                if (auth()->user()->can('so.update') && $row->status != 'void' && $row->status != 'cancelled' && $row->picking_status != 'INVOICED') {
                                    $edit_url = action([\App\Http\Controllers\SellController::class, 'edit'], [$row->id]);
                                    if (!empty($cid_url)) {
                                        $edit_url .= '?' . $cid_url;
                                    }
                                    $html .= '<li><a target="_blank" href="' . $edit_url . '"><i class="fas fa-edit"></i> ' . __('messages.edit') . '</a></li>';
                                }
                            } elseif ($row->is_direct_sale == 0) {
                                if (auth()->user()->can('sell.update')) {
                                    $edit_url = action([\App\Http\Controllers\SellPosController::class, 'edit'], [$row->id]);
                                    if (!empty($cid_url)) {
                                        $edit_url .= '?' . $cid_url;
                                    }
                                    $html .= '<li><a target="_blank" href="' . $edit_url . '"><i class="fas fa-edit"></i> ' . __('messages.edit') . '</a></li>';
                                }
                            } else {
                                if (auth()->user()->can('direct_sell.update') && $row->payment_status == 'due') {
                                    $edit_url = action([\App\Http\Controllers\SellController::class, 'edit'], [$row->id]);
                                    if (!empty($cid_url)) {
                                        $edit_url .= '?' . $cid_url;
                                    }
                                    $html .= '<li><a target="_blank" href="' . $edit_url . '"><i class="fas fa-edit"></i> ' . __('messages.edit') . '</a></li>';
                                }
                            }

                            // Add Cancel button for sales orders
                            if ($row->type == 'sales_order' && auth()->user()->can('so.delete') && $row->status != 'cancelled' && $row->status != 'void' && $row->picking_status != 'INVOICED') {
                                // Use the named WEB route explicitly to avoid accidentally
                                // hitting the API route for this action.
                                $cancelUrl = route('sells.cancelSalesOrder', [$row->id]);
                                $html .= '<li><a href="#" data-href="' . $cancelUrl . '" class="cancel-sales-order"><i class="fas fa-times" style="font-size: 20px;"></i> Cancel</a></li>';
                            }

                            $delete_link = '<li><a href="' . action([\App\Http\Controllers\SellPosController::class, 'destroy'], [$row->id]) . '" class="delete-sale "><i class="fas fa-trash"></i> ' . __('messages.delete') . '</a></li>';
                            $delete_link = '<li><a href="' . action([\App\Http\Controllers\SellPosController::class, 'voidSell'], [$row->id]) . '" class="delete-sale "><i class="fas fa-trash"></i> ' . 'Void' . '</a></li>';
                            if ($row->is_direct_sale == 0) {
                                if (auth()->user()->can('sell.delete') && $row->payment_status == 'due') {
                                    $html .= $delete_link;
                                }
                            } elseif ($row->type == 'sales_order'  && ($row->picking_status  !== 'INVOICED' || $row->picking_status == null)) {
                                if (auth()->user()->can('so.delete')) {
                                    $html .= $delete_link;
                                }
                            } else {
                                if (
                                    auth()->user()->can('direct_sell.delete')
                                    //   && $row->payment_status == 'due' 
                                    && $row->picking_status != 'INVOICED'
                                ) {
                                    $html .= $delete_link;
                                }
                            }
                        }

                        if (config('constants.enable_download_pdf') && auth()->user()->can('print_invoice') && $sale_type != 'sales_order') {
                            $html .= '<li><a href="' . route('sell.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true"></i> ' . __('lang_v1.download_pdf') . '</a></li>';

                            if (! empty($row->shipping_status)) {
                                $html .= '<li><a href="' . route('packing.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true"></i> ' . __('lang_v1.download_paking_pdf') . '</a></li>';
                            }
                        }

                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.access')) {
                            if (! empty($row->document)) {
                                $document_name = ! empty(explode('_', $row->document, 2)[1]) ? explode('_', $row->document, 2)[1] : $row->document;
                                $html .= '<li><a href="' . url('uploads/documents/' . $row->document) . '" download="' . $document_name . '"><i class="fas fa-download" aria-hidden="true"></i>' . __('purchase.download_document') . '</a></li>';
                                if (isFileImage($document_name)) {
                                    $html .= '<li><a href="#" data-href="' . url('uploads/documents/' . $row->document) . '" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>' . __('lang_v1.view_document') . '</a></li>';
                                }
                            }
                        }

                        if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {

                            if ($only_shipments) {

                                $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'shipmentDetails'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-file-invoice" aria-hidden="true"></i>' . 'View Shipment' . '</a></li>';
                            }
                            if ($row->shipping_status !== null && ($row->picking_status !== 'INVOICED' || $row->picking_status == null)) {
                                $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'order_shipped']) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>Send Shipment notification</a></li>';

                                // $html .= '<li><a href="#" ' . $row . 'data-href="' . url('shipment/' . $row->id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-truck" aria-hidden="true"></i>' . __('lang_v1.edit_shipping') . '</a></li>';
                            }
                        }
                        // if (auth()->user()->can('sell.update')) {
                        //     $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\OrderfulfillmentController::class, 'unLockModel'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-unlock-alt" aria-hidden="true"></i>' . 'Unlock Sell' . '</a></li>';
                        // }

                        if ($row->type == 'sell') {
                            if (auth()->user()->can('print_invoice')) {
                                $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fas fa-print" aria-hidden="true"></i> ' . __('lang_v1.print_invoice') . '</a></li>
                                    <li><a href="#" class="print-preview" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fas fa-eye" aria-hidden="true"></i> ' . __('lang_v1.print_preview') . '</a></li>
                                    <li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?package_slip=true"><i class="fas fa-file-alt" aria-hidden="true"></i> ' . __('lang_v1.packing_slip') . '</a></li>';

                                $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?delivery_note=true"><i class="fas fa-file-alt" aria-hidden="true"></i> ' . __('lang_v1.delivery_note') . '</a></li>';
                            }
                            $html .= '<li class="divider"></li>';
                            if (! $only_shipments) {
                                if (
                                    $row->is_direct_sale == 0 && ! auth()->user()->can('sell.update') &&
                                    auth()->user()->can('edit_pos_payment')
                                ) {
                                    $html .= '<li><a href="' . route('edit-pos-payment', [$row->id]) . '" 
                                    ><i class="fas fa-money-bill-alt"></i> ' . __('lang_v1.add_edit_payment') .
                                        '</a></li>';
                                }

                                if (
                                    auth()->user()->can('sell.payments') ||
                                    auth()->user()->can('edit_sell_payment') ||
                                    auth()->user()->can('delete_sell_payment')
                                ) {
                                    if ($row->payment_status != 'paid') {
                                        $html .= '<li><a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'addPayment'], [$row->id]) . '" class="add_payment_modal"><i class="fas fa-money-bill-alt"></i> ' . __('purchase.add_payment') . '</a></li>';
                                    }

                                    $html .= '<li><a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'show'], [$row->id]) . '" class="view_payment_modal"><i class="fas fa-money-bill-alt"></i> ' . __('purchase.view_payments') . '</a></li>';
                                }
                                if (auth()->user()->can('access_own_sell_return') || auth()->user()->can('access_sell_return')) {
                                    if ($row->payment_status != 'due') {
                                        $html .= '<li><a href="' . action([\App\Http\Controllers\SellReturnController::class, 'add'], [$row->id,$cid_url]) . '"><i class="fas fa-undo"></i> ' . __('lang_v1.sell_return') . '</a></li>';
                                    }
                                }
                                if (auth()->user()->can('sell.create') || auth()->user()->can('direct_sell.access')) {
                                    // $html .= '<li><a href="' . action([\App\Http\Controllers\SellController::class, 'duplicateSell'], [$row->id]) . '"><i class="fas fa-copy"></i> ' . __("lang_v1.duplicate_sell") . '</a></li>';

                                    $html .= '<li><a href="' . action([\App\Http\Controllers\SellPosController::class, 'showInvoiceUrl'], [$row->id]) . '" class="view_invoice_url"><i class="fas fa-eye"></i> ' . __('lang_v1.view_invoice_url') . '</a></li>';
                                }
                            }

                            $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'new_sale']) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>' . __('lang_v1.new_sale_notification') . '</a></li>';
                        } else {
                            $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'viewMedia'], ['model_id' => $row->id, 'model_type' => \App\Transaction::class, 'model_media_type' => 'shipping_document']) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-paperclip" aria-hidden="true"></i>' . __('lang_v1.shipping_documents') . '</a></li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="final-total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->editColumn(
                    'tax_amount',
                    '<span class="total-tax" data-orig-value="{{$tax_amount}}">@format_currency($tax_amount)</span>'
                )
                ->editColumn(
                    'total_paid',
                    '<span class="total-paid" data-orig-value="{{$total_paid}}">@format_currency($total_paid)</span>'
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="total_before_tax" data-orig-value="{{$total_before_tax}}">@format_currency($total_before_tax)</span>'
                )
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = ! empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (! empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->total_before_tax * ($discount / 100);
                        }

                        return '<span class="total-discount" data-orig-value="' . $discount . '">' . $this->transactionUtil->num_f($discount, true) . '</span>';
                    }
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        if ($row->total_paid) {
                            $diff = $row->final_total - $row->total_paid;
                            if ($diff == 0) {
                                return '<a href="#" class="label bg-green" value="' . "Paid" . '">Paid </a>';
                            } elseif ($diff == $row->final_total) {
                                return '<a href="#" class="label bg-yellow" value="' . "Due" . '">Due</a>';
                            } else if ($diff <= $row->final_total) {
                                return '<a href="#" class="label bg-info" value="' . "Partial" . '">Partial</a>';
                            }
                            return '<a href="#" class="label bg-yellow" value="' . "Due" . '">Due</a>';
                        } else {
                            return '<a href="#" class="label bg-yellow" value="' . "due" . '">Due</a>';
                        }
                    }
                )
                ->editColumn(
                    'types_of_service_name',
                    '<span class="service-type-label" data-orig-value="{{$types_of_service_name}}" data-status-name="{{$types_of_service_name}}">{{$types_of_service_name}}</span>'
                )
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining = $row->final_total - $row->total_paid;
                    if (!empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        if ($total_remaining < 0) {
                            $total_remaining = 0;
                        }
                        $total_remaining -= $return_due;
                        if ($total_remaining < 0) {
                            $total_remaining = 0;
                        }
                    }
                    $total_remaining_html = '<span class="payment_due" data-orig-value="' . $total_remaining . '">' .
                        $this->transactionUtil->num_f($total_remaining, true) .
                        '</span>';
                    return $total_remaining_html;
                })
                ->addColumn('return_due', function ($row) {
                    $return_due_html = '';
                    if (! empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $total_remaining = $row->final_total - $row->total_paid;
                        $return_due -= $total_remaining;
                        if ($return_due < 0) {
                            $return_due = 0;
                        }
                        $return_due_html .= '<a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'show'], [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="sell_return_due" data-orig-value="' . $return_due . '">' . $this->transactionUtil->num_f($return_due, true) . '</span></a>';
                    }

                    return $return_due_html;
                })
                ->editColumn('invoice_no', function ($row) use ($is_crm) {
                    $invoice_no = $row->invoice_no;
                    if (! empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                    if (! empty($row->return_exists)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') . '"><i class="fas fa-undo"></i></small>';
                    }
                    if (! empty($row->is_recurring)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.subscribed_invoice') . '"><i class="fas fa-recycle"></i></small>';
                    }

                    if (! empty($row->recur_parent_id)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="' . __('lang_v1.subscription_invoice') . '"><i class="fas fa-recycle"></i></small>';
                    }

                    if (! empty($row->is_export)) {
                        $invoice_no .= '</br><small class="label label-default no-print" title="' . __('lang_v1.export') . '">' . __('lang_v1.export') . '</small>';
                    }

                    if ($is_crm && ! empty($row->crm_is_order_request)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-yellow label-round no-print" title="' . __('crm::lang.order_request') . '"><i class="fas fa-tasks"></i></small>';
                    }

                    return $invoice_no;
                })
                ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
                    $status_color = ! empty($this->shipping_status_colors[$row->shipping_status]) ? $this->shipping_status_colors[$row->shipping_status] : 'bg-gray';
                    $status = ! empty($row->shipping_status) ? '<a href="#" class="btn-modal" data-href="' . action([\App\Http\Controllers\SellController::class, 'editShipping'], [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color . '">' . $shipping_statuses[$row->shipping_status] . '</span></a>' : '';

                    return $status;
                })
                ->addColumn('conatct_name', function ($data) {
                    $name = $data->name . ' ' . $data->supplier_business_name;
                    $id = $data->cid;
                    return '<a href="/contacts/' . $id . '?type=customer" target="_blank" > ' . $name . '</a>';
                })

                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('added_by', function ($row) {
                    $id = $row->uid ?? '';
                    if ($id) {

                        return '<a href="/users/' . $id . '" target="_blank">  <span  class="picker-gender tw-flex" data-toggle="tooltip" data-html="true" title="">' . $row->uname . '</span></a>';
                    } else {
                        return '<i class="fas fa-question"></i>';
                    }
                })
                ->editColumn('total_items', '{{@format_quantity($total_items)}}')
                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = $payment_types[$methods[0]] ?? '';
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = ! empty($payment_method) ? '<span class="payment-method" data-orig-value="' . $payment_method . '" data-status-name="' . $payment_method . '">' . $payment_method . '</span>' : '';

                    return $html;
                })
                ->editColumn('status', function ($row) use ($sales_order_statuses, $is_admin) {
                    $status = '';

                    if ($row->type == 'sales_order') {
                        if ($row->status == 'cancelled') {
                            $status = '<span class="label ' . 'bg-red' . '" >' . 'Cancelled' . '</span>';
                        } else if ($is_admin && $row->status != 'completed' && $row->status != 'void') {
                             $statusClass = $sales_order_statuses[$row->status]['class'] ?? 'bg-info';
                            $statusLabel = $sales_order_statuses[$row->status]['label'] ?? ucfirst($row->status);
                            $status = '<span class="edit-so-status label ' . $statusClass . '" data-href="' . action([\App\Http\Controllers\SalesOrderController::class, 'getEditSalesOrderStatus'], ['id' => $row->id]) . '">' . $statusLabel . '</span>';

                        } else {
                            $statusClass = $sales_order_statuses[$row->status]['class'] ?? 'bg-info';
                            $statusLabel = $sales_order_statuses[$row->status]['label'] ?? ucfirst($row->status);
                            $status = '<span class="label ' . $statusClass . '" >' . $statusLabel . '</span>';

                        }
                    }

                    return $status;
                })
                ->editColumn('so_qty_remaining', '{{@format_quantity($so_qty_remaining)}}')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                    'class' => function ($row) {
                        return $row->status == 'void' ? 'text-muted' : '';
                    },
                ]);

            $rawColumns = ['final_total', 'added_by', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due', 'conatct_name', 'status'];

            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $sales_representative = User::forDropdown($business_id, false, false, true);

        //Commission agent filter
        $is_cmsn_agent_enabled = request()->session()->get('business.sales_cmsn_agnt');
        $commission_agents = [];
        if (! empty($is_cmsn_agent_enabled)) {
            $commission_agents = User::forDropdown($business_id, false, true, true);
        }

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $sources = $this->transactionUtil->getSources($business_id);
        if ($is_woocommerce) {
            $sources['woocommerce'] = 'Woocommerce';
        }

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);


        return view('sell.index')
            ->with(compact('business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'is_types_service_enabled', 'shipping_statuses', 'sources', 'payment_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response | mixed
     */
    public function create()
    {
        $cid = request()->query('cid');
        // dd($cid);
        if ($cid) {
            $customer = Contact::find($cid);
            if($customer->contact_status=='inactive'){
                return back()->with('status', [
                'success' => 0,
                'msg' => "Customer is deactivated",
            ]);
            }
            
        }
        $sale_type = request()->get('sale_type', '');

        if ($sale_type == 'sales_order') {
            if (! auth()->user()->can('so.create')) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            if (! auth()->user()->can('direct_sell.access')) {
                abort(403, 'Unauthorized action.');
            }
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for users quota
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (! $this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action([\App\Http\Controllers\SellController::class, 'index']));
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $default_location = null;
        foreach ($business_locations as $id => $name) {
            $default_location = BusinessLocation::findOrFail($id);
            break;
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
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

        $payment_line = $this->dummyPaymentLine;
        $payment_lines[] = $this->dummyPaymentLine;
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        //Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $default_price_group_id = ! empty($default_location->selling_price_group_id) && array_key_exists($default_location->selling_price_group_id, $price_groups) ? $default_location->selling_price_group_id : null;

        $default_datetime = $this->businessUtil->format_date('now', true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        // Permissions for editing discount & price on create screen (used in POS partials)
        $edit_discount = auth()->user()->can('edit_product_discount_from_pos_screen');
        $edit_price = auth()->user()->can('edit_product_price_from_pos_screen');

        $invoice_schemes = InvoiceScheme::forDropdown($business_id);
        $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        if (! empty($default_location) && !empty($default_location->sale_invoice_scheme_id)) {
            $default_invoice_schemes = InvoiceScheme::where('business_id', $business_id)
                ->findorfail($default_location->sale_invoice_scheme_id);
        }
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        //Types of service
        $types_of_service = [];
        if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
            $types_of_service = TypesOfService::forDropdown($business_id);
        }

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }

        $status = request()->get('status', '');

        $statuses = Transaction::sell_statuses();

        if ($sale_type == 'sales_order') {
            $status = 'ordered';
        }

        $is_order_request_enabled = false;
        $is_crm = $this->moduleUtil->isModuleInstalled('Crm');
        if ($is_crm) {
            $crm_settings = Business::where('id', auth()->user()->business_id)
                ->value('crm_settings');
            $crm_settings = ! empty($crm_settings) ? json_decode($crm_settings, true) : [];

            if (! empty($crm_settings['enable_order_request'])) {
                $is_order_request_enabled = true;
            }
        }

        //Added check because $users is of no use if enable_contact_assign if false
        $users = config('constants.enable_contact_assign') ? User::forDropdown($business_id, false, false, false, true) : [];

        $change_return = $this->dummyPaymentLine;

        return view('sell.create')
            ->with(compact(
                'business_details',
                'taxes',
                'walk_in_customer',
                'business_locations',
                'bl_attributes',
                'default_location',
                'commission_agent',
                'types',
                'customer_groups',
                'payment_line',
                'payment_types',
                'price_groups',
                'default_datetime',
                'pos_settings',
                'invoice_schemes',
                'default_invoice_schemes',
                'types_of_service',
                'accounts',
                'shipping_statuses',
                'status',
                'sale_type',
                'statuses',
                'is_order_request_enabled',
                'users',
                'default_price_group_id',
                'change_return',
                'edit_discount',
                'edit_price',
                'payment_lines'
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | mixed
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response | mixed
     */
    public function show($id)
    {
        // if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access') && !auth()->user()->can('view_own_sell_only')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');
        $cid = request()->session()->get('user.id');
        $transaction = Transaction::where('business_id', $business_id)
            ->with(['price_group', 'types_of_service', 'media', 'media.uploaded_by_user'])
            ->whereIn('type', ['sell', 'sales_order'])
            ->findorfail($id);

        // session lock 

        $isLockModal = false;
        $orderFulfillmentController = app(OrderfulfillmentController::class);
        $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $id, true);
        if ($lockModal && $lockModal['status'] == false) {
            $isLockModal = true;
        }

        $taxes = TaxRate::where('business_id', $business_id)
            ->pluck('name', 'id');
        $query = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['contact', 'delivery_person_user', 'sell_lines' => function ($q) {
                $q->whereNull('parent_sell_line_id');
            }, 'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.product.second_unit', 'sell_lines.product.vendors', 'sell_lines.variations','sell_lines.variations.variation_location_details', 'sell_lines.variations.product_variation', 'sell_lines.variations.group_prices', 'sell_lines.variations.group_prices.groupInfo', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff', 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties', 'media']);

        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }
        $sell = $query->firstOrFail();
         $customer =  $sell->contact_id;
        $customer_status='';
        if ($customer) {
            $customer_data = Contact::find($customer);
             $customer_status=$customer_data->contact_status;
        }


        $activities = Activity::forSubject($sell)
            ->with(['causer', 'subject'])
            ->latest()
            ->get();

        $line_taxes = [];
        $totalTax = 0;
        foreach ($sell->sell_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
            //custom start total tax value 
            $totalTax += $value->item_tax * $value->quantity;
            //custom end total tax value 
            if (! empty($taxes[$value->tax_id])) {
                if (isset($line_taxes[$taxes[$value->tax_id]])) {
                    $line_taxes[$taxes[$value->tax_id]] += ($value->item_tax * $value->quantity);
                } else {
                    $line_taxes[$taxes[$value->tax_id]] = ($value->item_tax * $value->quantity);
                }
            }
        }


        $payment_types = $this->transactionUtil->payment_types($sell->location_id, true);
        $order_taxes = [];
        if (! empty($sell->tax)) {
            if ($sell->tax->is_tax_group) {
                $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
            } else {
                $order_taxes[$sell->tax->name] = $sell->tax_amount;
            }
        }

        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $shipping_status_colors = $this->shipping_status_colors;
        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = ! empty($common_settings['enable_product_warranty']) ? true : false;

        $statuses = Transaction::sell_statuses();

        if ($sell->type == 'sales_order') {
            $sales_order_statuses = Transaction::sales_order_statuses(true);
            $statuses = array_merge($statuses, $sales_order_statuses);
        }
        $status_color_in_activity = Transaction::sales_order_statuses();
        $sales_orders = $sell->salesOrders();


        //custom start total tax value 
        $customTotalTax = [];
        $customTotalTax['tax'] = $totalTax;
        //custom end total tax value

        $business_details = $this->businessUtil->getDetails($business_id);
        return view('sale_pos.show')
            ->with(compact(
                'taxes',
                'sell',
                'payment_types',
                'order_taxes',
                'pos_settings',
                'shipping_statuses',
                'shipping_status_colors',
                'is_warranty_enabled',
                'activities',
                'statuses',
                'status_color_in_activity',
                'sales_orders',
                'line_taxes',
                'customTotalTax',
                'isLockModal',
                'id','business_details','customer_status'
            ));
    }

    public function manualPick($id)
    {
        $manage_order_module = session()->get('business.manage_order_module');
        $isVerifier = request()->query('type') == 'verifier' ? true : false;
        $is_api = false;
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $is_api = true;
        } catch (\Throwable $th) {
            $staff = auth()->user();
        }
        $is_admin = $this->businessUtil->is_admin($staff);

        // erp module permission check
        if ($manage_order_module == 'manual') {
            return response()->json(['status' => false, 'message' => 'Manual picking is not allowed']);
        } else if ($manage_order_module == 'pickerApp' && $is_api == false) {
            return response()->json(['status' => false, 'message' => 'Picking is allowed in picker app']);
        } else {
            // allow manual picking and picker app picking both 
        }

        if (!$staff->can('sell.view') && !$staff->can('direct_sell.access') && !$staff->can('view_own_sell_only') && !$staff->can('pickerman')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $staff->business_id;

        try {

            $pickingOrders = Transaction::with(['sell_lines' => function ($query) {
                $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'picked_quantity', 'ordered_quantity', 'is_picked');
            }])
                ->where('type', 'sales_order')
                ->where('id', $id)
                ->when(1, function ($query) use ($staff, $isVerifier) {
                    if ($isVerifier) {
                        $query->where('isPicked', 1)->where('verifierID', $staff->id);
                    } else {
                        $query->where('pickerID', $staff->id);
                    }
                })
                ->first();
            if (!$pickingOrders) {
                if ($isVerifier && !$is_api) {
                    return redirect('/order-fulfillment')->with('status', ['success' => 0, 'msg' => 'Picked order not found for verification']);
                }
                return response()->json(['status' => false, 'message' => 'Check Picking status or verification assignee']);
            }

            if ((request()->ajax() || $is_api) && ($staff->can('pickerman') || $is_admin)) {
                $lines = TransactionSellLine::where('transaction_id', $id)
                    ->with(['product', 'variations.variation_location_details'])
                    ->get();
                $totalOrdered = 0;
                $totalPicked = 0;

                foreach ($lines as $line) {
                    $totalOrdered += $line->ordered_quantity;
                    $totalPicked += $line->is_picked ? $line->ordered_quantity : $line->picked_quantity;
                }
                $fulfilledPercentage = $totalOrdered > 0 ? ($totalPicked / $totalOrdered) * 100 : 0;
                if ($is_api) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Picking order found',
                        'data' => $lines,
                        'fulfilledPercentage' => $fulfilledPercentage,
                        'totalOrdered' => $totalOrdered,
                        'totalPicked' => $totalPicked,
                        'start_time' => $pickingOrders->picking_started_at ?? null,
                        'end_time' => $pickingOrders->picking_ended_at ?? null,
                        'isPicked' => $pickingOrders->isPicked,
                        'isVerified' => $pickingOrders->isVerified,
                    ]);
                }

                return DataTables::of($lines)
                    ->addColumn('product_name', fn($row) => optional($row->product)->name)
                    ->addColumn('sku', fn($row) => optional($row->variations)->sub_sku)
                    ->addColumn('barcode', fn($row) => optional($row->variations)->var_barcode_no)
                    ->addColumn('ordered_quantity', fn($row) => $row->ordered_quantity)
                    ->addColumn('in_hand_stock', function ($row) {
                        return optional(optional($row->variations)->variation_location_details->first())->qty_available ?? 0;
                    })
                    ->addColumn('picked_quantity_input', function ($row) {
                        $variation = optional($row->variations);
                        $product = optional($row->product);
                        $stock = optional($variation->variation_location_details->first())->qty_available ?? 0;
                        $enable_stock = $product->enable_stock ?? 1;
                        if (request()->input('type') == 'verifier') {
                            return '<input type="number" class="form-control inline-pick"
                            data-line-id="' . $row->id . '" 
                            data-barcode="' . e($variation->var_barcode_no) . '" 
                            data-sub-sku="' . e($variation->sub_sku) . '" 
                            data-max="' . $row->ordered_quantity . '" 
                            data-stock="' . $stock . '" 
                            data-enable-stock="' . $enable_stock . '" 
                            value="' . $row->verified_qty . '" min="0">';
                        } else {

                            return '<input type="number" class="form-control inline-pick"
                                data-line-id="' . $row->id . '" 
                                data-barcode="' . e($variation->var_barcode_no) . '" 
                                data-sub-sku="' . e($variation->sub_sku) . '" 
                                data-max="' . $row->ordered_quantity . '" 
                                data-stock="' . $stock . '" 
                                data-enable-stock="' . $enable_stock . '" 
                                value="' . $row->picked_quantity . '" min="0">';
                        }
                    })
                    ->with(['fulfilled_percentage' => round($fulfilledPercentage, 2)])
                    ->rawColumns(['picked_quantity_input'])
                    ->make(true);
            }

            // Non-ajax request: load the view
            $items = [];
            $totalitem = $pickedItem = 0;

            foreach ($pickingOrders->sell_lines as $line) {
                $totalitem += $line->ordered_quantity;
                $pickedItem += $line->picked_quantity;
            }

            $fulfilledPercentage = $totalitem > 0 ? ($pickedItem / $totalitem) * 100 : 0;

            return view('order_fulfillment.picker-order', compact(
                'id',
                'pickingOrders',
                'fulfilledPercentage'
            ));
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function manualPickPopup($id)
    {
        $manage_order_module = session()->get('business.manage_order_module');
        $isVerifier = request()->query('type') == 'verifier' ? true : false;
        $is_api = false;
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $is_api = true;
        } catch (\Throwable $th) {
            $staff = auth()->user();
        }
        $is_admin = $this->businessUtil->is_admin($staff);

        // erp module permission check
        if ($manage_order_module == 'pickerApp' && $is_api == false) {
            return response()->json(['status' => false, 'message' => 'Picking is allowed in picker app']);
        }

        if (!$staff->can('sell.view') && !$staff->can('direct_sell.access') && !$staff->can('view_own_sell_only') && !$staff->can('pickerman')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $staff->business_id;

        try {

            $pickingOrders = Transaction::with(['sell_lines' => function ($query) {
                $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'picked_quantity', 'ordered_quantity', 'is_picked', 'manual_picked_qty', 'barcode_picked_qty', 'shorted_picked_qty', 'verified_qty','isVerified');
            }, 'sell_lines.product', 'sell_lines.variations.variation_location_details', 'sell_lines.variations.media'])
                ->where('type', 'sales_order')
                ->where('id', $id)
                ->when(1, function ($query) use ($staff, $isVerifier) {
                    if ($isVerifier) {
                        $query->where('isPicked', 1)->where('verifierID', $staff->id);
                    } else {
                        $query->where('pickerID', $staff->id);
                    }
                })
                ->first();
            if (!$pickingOrders) {
                if ($isVerifier) {
                    // lets asgin the order to staff 
                    $pickingOrders = Transaction::with(['sell_lines' => function ($query) {
                        $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'picked_quantity', 'ordered_quantity', 'is_picked', 'manual_picked_qty', 'barcode_picked_qty', 'shorted_picked_qty', 'verified_qty','isVerified');
                    }, 'sell_lines.product', 'sell_lines.variations.variation_location_details', 'sell_lines.variations.media'])
                        ->where('type', 'sales_order')
                        ->where('id', $id)
                        ->first();
                    if ($pickingOrders && $pickingOrders->isPicked == 0) {
                        return response()->json(['status' => false, 'message' => 'Order is not picked yet']);
                    } else if (!$pickingOrders) {
                        return response()->json(['status' => false, 'message' => 'Not found']);
                    }
                    if($pickingOrders->verifierID == null){
                        $pickingOrders->verifierID = $staff->id;
                        $pickingOrders->save();
                    } else {
                        return response()->json(['status' => false, 'message' => 'Assign verifier to pick the order']);
                    }
                } else {
                    return response()->json(['status' => false, 'message' => 'Check Picking status or verification assignee']);
                }
            }
            if (($staff->can('pickerman') || $is_admin)) {
                if ($pickingOrders->isPicked == true && !$isVerifier) {
                    return response()->json(['status' => false, 'message' => 'Order is already picked']);
                }
                if (count($pickingOrders->sell_lines) == 0) {
                    return response()->json(['status' => false, 'message' => 'No items to pick']);
                }
                $lines = $pickingOrders->sell_lines;
                $totalOrdered = 0;

                $totalPicked = 0;
                $actualPicked = 0;
                
                foreach ($lines as $line) {
                    $totalOrdered += $line->ordered_quantity;
                    $totalPicked += $line->is_picked ? $line->ordered_quantity : $line->picked_quantity;
                    $actualPicked += $line->picked_quantity;
                    // barcode null ->sub_sku 
                    // $line->variations->var_barcode_no = $line->variations->var_barcode_no ?? $line->variations->sub_sku;
                }
                $fulfilledPercentage = $totalOrdered > 0 ? ($totalPicked / $totalOrdered) * 100 : 0;

                $data = [
                    'status' => true,
                    'message' => 'Picking order found',
                    'data' => $pickingOrders,
                    'fulfilledPercentage' => $fulfilledPercentage,
                    'totalOrdered' => $totalOrdered,
                    'totalPicked' => $actualPicked,
                    'start_time' => $pickingOrders->picking_started_at ?? null,
                    'end_time' => $pickingOrders->picking_ended_at ?? null,
                    'isVerifier' => $isVerifier,
                    'totalVerified' => $totalVerified ?? 0,
                ];
                // return response()->json($data);
                return view('order_fulfillment.partials.manual_pick_verify', $data);
            } else {
                return response()->json(['status' => false, 'message' => 'You are not authorized to pick this order']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function sellPickVerifyData($id){
        $pickingOrders = Transaction::with(['sell_lines' => function ($query) {
            $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'picked_quantity', 'ordered_quantity', 'is_picked', 'manual_picked_qty', 'barcode_picked_qty', 'shorted_picked_qty', 'verified_qty');
        }, 'sell_lines.product', 'sell_lines.variations.variation_location_details', 'sell_lines.variations.media'])
            ->where('type', 'sales_order')
            ->where('id', $id)
            ->first();
        if(!$pickingOrders){
            return response()->json(['status' => false, 'message' => 'Picking order not found']);
        }
        return view('order_fulfillment.partials.sell_pick_verify_data',compact('pickingOrders'));
    }
    public function manualPickStore(Request $request)
    {
        $manage_order_module = session()->get('business.manage_order_module');
        $isVerifier = request()->query('type') == 'verifier' || request()->input('type') == 'verifier' ? true : false;
        $is_api = false;
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $is_api = true;
        } catch (\Throwable $th) {
            $staff = auth()->user();
        }
        // erp module permission check
        if ($manage_order_module == 'pickerApp' && $is_api == false) {
            return response()->json(['status' => false, 'message' => 'Picking is allowed in picker app']);
        }
        $is_admin = $this->businessUtil->is_admin($staff);
        if (! $is_admin && ! $staff->can('pickerman')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $staff->business_id;
        DB::beginTransaction();

        try {
            $pickingOrder = Transaction::with(['sell_lines' => function ($query) {
                $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'picked_quantity', 'ordered_quantity', 'barcode_picked_qty', 'manual_picked_qty', 'shorted_picked_qty', 'verified_qty');
            }])
                ->where(function ($query) use ($staff, $isVerifier, $is_api) {
                    if ($is_api) {
                        if ($isVerifier) {
                            $query->where('verifierID', $staff->id);
                        } else {
                            $query->where('pickerID', $staff->id);
                        }
                    } else {
                        if($isVerifier){
                            $query->where('verifierID', $staff->id)->where('isPicked', 1);
                        } else {
                            $query->where('pickerID', $staff->id);
                        }
                    }
                })
                ->where('type', 'sales_order')
                ->where('id', $request->input('transaction_id'))
                ->first();
            if (!$pickingOrder) {
                return response()->json(['status' => false, 'message' => 'Picking order not found or you are not authorized to pick this order.']);
            }
            // session lock 
            $isLockModal = false;
            $orderFulfillmentController = app(OrderfulfillmentController::class);
            $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $pickingOrder->id,true);
            if($lockModal['status'] == false){
                return response()->json(['status' => false, 'message' => $lockModal['message']]);
            }
            $updatedLine = [];
            
            // Group barcode requests by barcode/SKU to handle multiple requests for same product
            $barcodeRequests = [];
            $manualRequests = [];
            
            foreach ($request->picked_quantity as $lineId => $pickedQuantity) {
                $pickingType = $request->type;
                if ((int) $pickedQuantity < 0) {
                    return response()->json(['status' => false, 'message' => 'Not allowed negative values']);
                }

                if ($pickingType == 'barcode') {
                    // Group barcode requests by the barcode/SKU
                    $barcodeRequests[$lineId] = $pickedQuantity;
                } else {
                    // Manual requests are processed individually
                    $manualRequests[$lineId] = $pickedQuantity;
                }
            }
            
            // Process barcode requests with prioritization
            foreach ($barcodeRequests as $lineId => $pickedQuantity) {
                $variation = Variation::with(['product', 'variation_location_details'])
                    ->where('var_barcode_no', $lineId)
                    ->first();
                if (!$variation) {
                    $variation = Variation::with(['product', 'variation_location_details'])
                        ->where('sub_sku', $lineId)
                        ->first();
                }
                if (!$variation) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Barcode/SKU not found in system',
                        'type' => 'invalid_barcode'
                    ]);
                }
                
                // Find all sell lines for this variation and prioritize incomplete ones
                $matchingSellLines = $pickingOrder->sell_lines->filter(function ($line) use ($variation) {
                    return $line->variation_id == $variation->id;
                });
                
                if ($matchingSellLines->isEmpty()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Product does not belong to this order',
                        'type' => 'wrong_order',
                        'product' => [
                            'name' => $variation->product->name,
                            'sku' => $variation->sub_sku,
                            'barcode' => $variation->var_barcode_no,
                            'image' => $variation->product->image_url ?? null
                        ],
                        'lineId' => $lineId,
                        'reason' => 'no_sell_line'
                    ]);
                }
                
                // Sort sell lines by remaining quantity (incomplete lines first)
                $sortedSellLines = $matchingSellLines->sortByDesc(function ($line) use ($isVerifier) {
                    if ($isVerifier) {
                        // For verifier mode, sort by remaining verification (picked_qty - verified_qty)
                        return $line->picked_quantity - $line->verified_qty;
                    } else {
                        // For picker mode, sort by remaining picking (ordered_qty - picked_qty)
                        return $line->ordered_quantity - $line->picked_quantity;
                    }
                });
                
                // Use the first (most incomplete) line
                $sellLine = $sortedSellLines->first();
                
                // Check if the line is already completed
                $isCompleted = $isVerifier ? 
                    ($sellLine->verified_qty >= $sellLine->picked_quantity || $sellLine->picked_quantity <= 0) : 
                    ($sellLine->picked_quantity >= $sellLine->ordered_quantity);
                
                if (!$isCompleted) {
                    // Process the barcode request
                    $this->processSellLine($sellLine, $pickedQuantity, $request->type, $isVerifier, $business_id, $updatedLine);
                } else {
                    // Skip processing if already completed
                    Log::info('Barcode scanned but line is already completed: ' . $lineId);
                }
            }
            
            // Process manual requests
            foreach ($manualRequests as $lineId => $pickedQuantity) {
                $sellLine = $pickingOrder->sell_lines->where('id', $lineId)->first();

                if (!$sellLine) {
                    return response()->json(['status' => false, 'message' => 'No sell line found for line_id: ' . $lineId, 'lineId' => $lineId, 'reason' => 'no_sell_line']);
                }

                // Process the manual request
                $this->processSellLine($sellLine, $pickedQuantity, $request->type, $isVerifier, $business_id, $updatedLine);
            }

            $pickingOrder->save();
            DB::commit();

            // Calculate order picked percentage
            $totalOrdered = 0;
            $totalPicked = 0;
            foreach ($pickingOrder->sell_lines as $line) {
                if($isVerifier){
                    $totalPicked += $line->verified_qty;
                    $totalOrdered += $line->picked_quantity;
                } else {
                    $totalOrdered += $line->ordered_quantity;
                    $totalPicked += $line->is_picked ? $line->ordered_quantity : $line->picked_quantity;
                }
            }
            $pickedPercentage = $totalOrdered > 0 ? round(($totalPicked / $totalOrdered) * 100, 2) : 0;
            if (!empty($updatedLine)) {
                return response()->json([
                    'status' => true,
                    'message' => $isVerifier ? 'Quantity verified successfully' : 'Quantity picked successfully',
                    'fulfilled_percentage' => $pickedPercentage,
                    'updated_line' => $updatedLine ?? []
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Limit Reached',
                    'fulfilled_percentage' => $pickedPercentage,
                    'updated_line' =>[]
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'An error occurred while processing the request: ' . $e->getMessage()]);
        }
    }

    /**
     * Process a sell line for picking/verification
     */
    private function processSellLine($sellLine, $pickedQuantity, $pickingType, $isVerifier, $business_id, &$updatedLine)
    {
        // Get the variation stock details
        $variation = Variation::with('variation_location_details', 'product')
            ->where('id', $sellLine->variation_id)
            ->first();

        if (!$variation) {
            throw new \Exception('Product variation not found for line ID ' . $sellLine->id);
        }

        // Get product to check enable_stock
        $product = $variation->product;
        $enable_stock = $product ? ($product->enable_stock ?? 1) : 1;

        // Get business settings for overselling
        $business = Business::find($business_id);
        $pos_settings = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);
        $allow_overselling = !empty($pos_settings['allow_overselling']) ? true : false;

        // Only get location if stock management is enabled
        $location = null;
        if ($enable_stock == 1) {
            if ($allow_overselling) {
                $location = $variation->variation_location_details->first();
            } else {
                $location = $variation->variation_location_details->firstWhere('qty_available', '>=', 0);
                if(!$location){
                    Log::warning('Case failure at SellController.php Log 1389' . $sellLine->id);
                    $location = $variation->variation_location_details->first();
                }
            }
            if (!$location) {
                throw new \Exception('No available stock found for variation of line ID ' . $sellLine->id);
            }
        }

        if ($isVerifier) {
            // verifier can only verify the quantity
            $check = false;
            if ($pickedQuantity > $sellLine->ordered_quantity) {
                $pickedQuantity = $sellLine->ordered_quantity;
                $check = true;
            }
            $sellLine->verified_qty = $pickedQuantity;

            if($pickingType == 'markVerified' && $sellLine->isVerified == 0) {
                $sellLine->isVerified = 1;    
            }

            $updatedLine[] = [
                'line_id' => $sellLine->id,
                'qty_available' => $location ? $location->qty_available : 0,
                'ordered_qty' => $sellLine->ordered_quantity,
                'picked_qty' => (int) $sellLine->picked_quantity,
                'manual_picked_qty' => $sellLine->manual_picked_qty,
                'barcode_picked_qty' => $sellLine->barcode_picked_qty,
                'shorted_picked_qty' => $sellLine->shorted_picked_qty,
                'is_picked' => $sellLine->is_picked ?? 0,
                'verified_qty' => $pickedQuantity,
                'isVerified' => $sellLine->isVerified ?? 0,
                'check' => $check
            ];
        } else {
            $availableStock = $location ? ($location->qty_available ?? 0) : 0;
            $previousPickedQuantity = $sellLine->picked_quantity;

            if ($pickedQuantity > $sellLine->ordered_quantity) {
                throw new \Exception('Picked quantity cannot exceed ordered quantity for line ID ' . $sellLine->id . '.');
            }

            // When marking as shorted, skip stock check (picker is recording that they could not pick full qty, not claiming more stock)
            $isShorted = ($pickingType === 'shorted');
            if (!$isShorted) {
                // Only check stock if enable_stock == 1 and not overselling
                if ($enable_stock == 1 && !$allow_overselling && $pickedQuantity > $availableStock) {
                    throw new \Exception('Insufficient stock available for line ID ' . $sellLine->id . '. Available: ' . $availableStock);
                }
            }

            if($pickingType != 'shorted'){
                $sellLine->is_picked = false;
                $sellLine->shorted_picked_qty = 0;
            }
            $pickedQuantityDifference = $pickedQuantity - $previousPickedQuantity;
            
            if ($pickingType === 'manual') {
                $sellLine->manual_picked_qty = ($sellLine->manual_picked_qty ?? 0) + $pickedQuantityDifference;
            } elseif ($pickingType === 'barcode') {
                $sellLine->barcode_picked_qty = ($sellLine->barcode_picked_qty ?? 0) + $pickedQuantityDifference;
            } elseif ($pickingType === 'shorted') {
                $sellLine->shorted_picked_qty = $sellLine->ordered_quantity - (
                    ($sellLine->manual_picked_qty ?? 0) + ($sellLine->barcode_picked_qty ?? 0)
                );
                $sellLine->is_picked = true;
            }
            $sellLine->picked_quantity = ($sellLine->manual_picked_qty ?? 0) + ($sellLine->barcode_picked_qty ?? 0);

            // When marking as shorted we do not change stock (no additional pick)
            $shouldUpdateStock = ($enable_stock == 1 && $location && !$isShorted);
            if ($shouldUpdateStock) {
                $location->qty_available -= $pickedQuantityDifference;
            }
            
            $updatedLine[] = [
                'line_id' => $sellLine->id,
                'qty_available' => $location ? $location->qty_available : 0,
                'ordered_qty' => $sellLine->ordered_quantity,
                'picked_qty' => (int) $sellLine->picked_quantity,
                'manual_picked_qty' => $sellLine->manual_picked_qty,
                'barcode_picked_qty' => $sellLine->barcode_picked_qty,
                'shorted_picked_qty' => $sellLine->shorted_picked_qty,
                'is_picked' => $sellLine->is_picked ?? 0,
            ];
        }
        
        // Only save location if stock management is enabled and location exists
        if ($enable_stock == 1 && $location) {
            $location->save();
        }
        $sellLine->save();
    }

    public function manualPack($id)
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access') && !auth()->user()->can('view_own_sell_only')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
            ->pluck('name', 'id');
        try {
            $packingOrder = Transaction::with([
                'sell_lines' => function ($query) {
                    $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'picked_quantity', 'ordered_quantity');
                },
                'shippingStation'
            ])
                // ->where('pickerID', request()->session()->get('user.id'))
                ->where('type', 'sales_order')
                ->where('id', $id)
                ->first();

            if (!$packingOrder) {
                return response()->json(['status' => false, 'message' => 'Picking order not found']);
            }
            $user = Contact::where('id', $packingOrder->contact_id)->first();
            $shipstation = ShipStation::where('usable', 1)->orderBy('priority', 'desc')->get();
            
            // Load shipping stations for the dropdown
            $shippingStations = \App\ShippingStation::where('business_id', $business_id)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
            
            // Load the transaction with shipping station relationship
            $packingOrder->load('shippingStation');
            
            return view('sale_pos.show_packing_model')
                ->with(compact(
                    'packingOrder',
                    'user',
                    'shipstation',
                    'shippingStations'
                ));
            // return response()->json(['status' => true, 'order_details' => $orderDetails]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
    public function manualPackStore(Request $request)
    {
        Log::info('manualPackStore method called', [
            'transaction_id' => $request->transaction_id ?? null,
            'business_id' => request()->session()->get('user.business_id') ?? null
        ]);
        
        $business_id = request()->session()->get('user.business_id');
        // return $request;
        DB::beginTransaction();

        try {
            $staff = request()->session()->get('user.id');
            $pickingOrder = Transaction::with(['sell_lines' => function ($query) {
                $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'picked_quantity', 'ordered_quantity');
            }])
                ->where('pickerID', $staff)
                ->where('type', 'sales_order')
                ->where('id', $request->transaction_id)
                ->first();
            if (!$pickingOrder) {
                return response()->json(['status' => false, 'message' => 'Picking order not found or you are not authorized to pick this order.']);
            }
            // session lock 
            $isLockModal = false;
            $orderFulfillmentController = app(OrderfulfillmentController::class);
            $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $pickingOrder->id,true);
            if($lockModal['status'] == false){
                return response()->json(['status' => false, 'message' => $lockModal['message']]);
            }
            $shipmentData = [];
            $length = $request->input('length'); //
            $width = $request->input('width'); //
            $height = $request->input('height'); //
            $weight = $request->input('weight'); //

            $shipment = $request->input('shipment_type'); //

            //
            // charges 
            //
            $rowCount = count($length);
            for ($i = 0; $i < $rowCount; $i++) {
                $shipmentData[] = [
                    'length' => $length[$i],
                    'width' => $width[$i],
                    'height' => $height[$i],
                    'weight' => $weight[$i],
                ];
            }
            $shipcharge = 0;
            $shipTracking = null;
            if ($shipment == 'pickup') {
                $shipcharge = 0.0000;
            } else if ($shipment == 'own') {
                $shipcharge = $request->input('own_charge'); //
                $shipTracking = $request->input('own_tracking'); //
            } else if ($shipment == 'ups') {
                $shipcharge = $request->input('ups_charge'); //
                $shipTracking = $request->input('ups_tracking');
            } else {
                return response()->json(['status' => true, 'message' => 'Invalid shipment type.']);
            }
            $shipcharge = number_format((float)$shipcharge, 4, '.', '');
            // $pickingOrder->shipment = $shipmentData;
            $pickingOrder->picking_status = 'PACKED';
            $pickingOrder->isVerified = true;
            $pickingOrder->shipping_charges = $shipcharge;
            $pickingOrder->save();
            
            // Automatically create tracking status when order is packed
            \App\Models\OrderTrackingStatus::updateOrCreate(
                [
                    'transaction_id' => $pickingOrder->id,
                    'status' => 'packed',
                ],
                [
                    'status_date' => now(),
                    'updated_by' => $staff,
                ]
            );
            
            // Send notification to customer when order is packed
            try {
                Log::info('Attempting to send order packed notification', [
                    'transaction_id' => $pickingOrder->id,
                    'contact_id' => $pickingOrder->contact_id,
                    'business_id' => $business_id
                ]);
                
                $contact = Contact::find($pickingOrder->contact_id);
                if ($contact) {
                    Log::info('Contact found for order packed notification', [
                        'transaction_id' => $pickingOrder->id,
                        'contact_id' => $contact->id,
                        'contact_email' => $contact->email ?? 'no email',
                        'contact_mobile' => $contact->mobile ?? 'no mobile'
                    ]);
                    
                    // // Dispatch notification job using order_packed notification type
                    // SendNotificationJob::dispatch(
                    //     false, // is_custom = false (using regular notification)
                    //     $business_id,
                    //     'order_packed', // Using order_packed notification type
                    //     null, // user
                    //     $contact,
                    //     $pickingOrder // transaction
                    // );
                    Log::info('Order packed notification queued successfully', [
                        'transaction_id' => $pickingOrder->id,
                        'contact_id' => $contact->id,
                        'business_id' => $business_id
                    ]);
                    
                    // Send Firebase push notification for PACKED status
                    $notificationUtil = new \App\Utils\NotificationUtil();
                    $notificationUtil->sendPushNotification(
                        'Order Packed',
                        'Your order #' . $pickingOrder->invoice_no . ' has been packed and is ready for shipment.',
                        $pickingOrder->contact_id,
                        [
                            'order_id' => $pickingOrder->id,
                            'invoice_no' => $pickingOrder->invoice_no,
                            'status' => 'packed',
                            'type' => 'order_status_update'
                        ],
                        'non_urgent'
                    );
                } else {
                    Log::warning('Contact not found for order packed notification', [
                        'transaction_id' => $pickingOrder->id,
                        'contact_id' => $pickingOrder->contact_id
                    ]);
                }
            } catch (\Exception $notificationError) {
                Log::error('Failed to queue order packed notification', [
                    'transaction_id' => $pickingOrder->id,
                    'contact_id' => $pickingOrder->contact_id ?? null,
                    'error' => $notificationError->getMessage(),
                    'file' => $notificationError->getFile(),
                    'line' => $notificationError->getLine(),
                    'trace' => $notificationError->getTraceAsString()
                ]);
                // Don't fail the whole operation if notification fails
            }
            
            DB::commit();
            // $contact= Contact::where('id',$pickingOrder->contact_id)->first();
            // return redirect()->to(');

            return response()->json(['status' => true,   'message' => 'Done']);
            // return response()->json(['status' => true,   'url' => "/sells/create?transaction_id=" . $pickingOrder->id . "&customer_id=" . $contact->contact_id
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('manualPackStore Error', [
                'transaction_id' => $request->transaction_id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false, 
                'message' => 'An error occurred while processing the request: ' . $e->getMessage(),
                'error_details' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ]);
        }
    }

    public function saleInvoiceCreate($id)
    {

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
            ->pluck('name', 'id');
        $query = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['contact', 'delivery_person_user', 'sell_lines' => function ($q) {
                $q->whereNull('parent_sell_line_id');
            }, 'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.product.second_unit', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff', 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties', 'media']);

        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }

        $sell = $query->firstOrFail();
        // ==========================================
        // PARENT-ONLY INVOICE CREATION ENFORCEMENT
        // ==========================================
        
        // Check if this is a child order - block invoice creation
        if ($sell->isChildOrder()) {
            $parentOrder = $sell->getParentOrder();
            $parentInvoiceNo = $parentOrder ? $parentOrder->invoice_no : 'N/A';
            $parentId = $parentOrder ? $parentOrder->id : null;
            
            // Return HTML error modal instead of JSON
            return view('sale_pos.partials.invoice_error_modal', [
                'error_title' => 'Cannot Create Invoice - Child Order',
                'error_message' => 'This is a child/split order. Invoices can only be created from the parent order.',
                'error_icon' => 'fas fa-exclamation-triangle',
                'error_color' => '#f59e0b',
                'show_parent_link' => true,
                'parent_invoice_no' => $parentInvoiceNo,
                'parent_id' => $parentId,
                'current_order' => $sell
            ]);
        }

        // Check if this order already has an invoice
        if ($sell->hasInvoice()) {
            return view('sale_pos.partials.invoice_error_modal', [
                'error_title' => 'Invoice Already Exists',
                'error_message' => 'An invoice has already been created for this order. Each order can only have one invoice.',
                'error_icon' => 'fas fa-file-invoice',
                'error_color' => '#3b82f6',
                'show_parent_link' => false,
                'current_order' => $sell
            ]);
        }

        // Get child orders info for parent orders
        $childOrders = collect([]);
        $consolidatedLines = collect([]);
        $isParentOrder = $sell->isParentOrder();
        
        if ($isParentOrder) {
            $childOrders = $sell->getChildOrders();
            // Get consolidated sell lines from all child orders for display
            $consolidatedLines = $sell->getConsolidatedSellLines();
        }

        // session lock 
        $isLockModal = false;
        $orderFulfillmentController = app(OrderfulfillmentController::class);
        $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $sell->id,true);
        if($lockModal['status'] == false){
            return response()->json(['status' => false, 'message' => $lockModal['message']]);
        }
        $activities = Activity::forSubject($sell)
            ->with(['causer', 'subject'])
            ->latest()
            ->get();

        $line_taxes = [];
        $totalTax = 0;
          $linesToProcess = $isParentOrder && $consolidatedLines->isNotEmpty() ? $consolidatedLines : $sell->sell_lines;
        
        foreach ($linesToProcess as $key => $value) {

            if (! empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $linesToProcess[$key] = $formated_sell_line;
            }
            //custom start total tax value 
            $totalTax += $value->item_tax * $value->quantity;
            //custom end total tax value 
            if (! empty($taxes[$value->tax_id])) {
                if (isset($line_taxes[$taxes[$value->tax_id]])) {
                    $line_taxes[$taxes[$value->tax_id]] += ($value->item_tax * $value->quantity);
                } else {
                    $line_taxes[$taxes[$value->tax_id]] = ($value->item_tax * $value->quantity);
                }
            }
        }


        $payment_types = $this->transactionUtil->payment_types($sell->location_id, true);
        $order_taxes = [];
        if (! empty($sell->tax)) {
            if ($sell->tax->is_tax_group) {
                $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
            } else {
                $order_taxes[$sell->tax->name] = $sell->tax_amount;
            }
        }

        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $shipping_status_colors = $this->shipping_status_colors;
        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = ! empty($common_settings['enable_product_warranty']) ? true : false;

        $statuses = Transaction::sell_statuses();

        if ($sell->type == 'sales_order') {
            $sales_order_statuses = Transaction::sales_order_statuses(true);
            $statuses = array_merge($statuses, $sales_order_statuses);
        }
        $status_color_in_activity = Transaction::sales_order_statuses();
        $sales_orders = $sell->salesOrders();


        //custom start total tax value 
        $customTotalTax = [];
        $customTotalTax['tax'] = $totalTax;
        //custom end total tax value

        return view('sale_pos.show_create_invoice_model')
            ->with(compact(
                'taxes',
                'sell',
                'payment_types',
                'order_taxes',
                'pos_settings',
                'shipping_statuses',
                'shipping_status_colors',
                'is_warranty_enabled',
                'activities',
                'statuses',
                'status_color_in_activity',
                'sales_orders',
                'line_taxes',
                'customTotalTax',
                'isParentOrder',
                'childOrders',
                'consolidatedLines'
            ));
        // return response()->json(['status' => true, 'order_details' => $orderDetails]);

    }
    public function saleInvoiceStore(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        // Get business settings for overselling
        $business = Business::find($business_id);
        $pos_settings = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);
        $allow_overselling = !empty($pos_settings['allow_overselling']) ? true : false;

        $id = (int) $request->input('sale_invoice_no');
        $query = Transaction::with(['contact', 'sell_lines', 'sell_lines.product'])->where('business_id', $business_id)
            ->where('id', $id)->first();
        $final_total = 0;
        $totalBeforeTax = 0;
        $payedAmount = 0;
        
        // ==========================================
        // PARENT-ONLY INVOICE CREATION ENFORCEMENT
        // ==========================================
        
        // Check if this is a child order - block invoice creation
        if ($query->isChildOrder()) {
            $parentOrder = $query->getParentOrder();
            $parentInfo = $parentOrder ? " Please create the invoice from Parent Order: {$parentOrder->invoice_no}" : "";
            return response()->json([
                'status' => false, 
                'msg' => "Invoice cannot be created for child/split orders.{$parentInfo}"
            ]);
        }

        // Check if this order already has an invoice
        if ($query->hasInvoice()) {
            return response()->json([
                'status' => false, 
                'msg' => "An invoice has already been created for this order. Each order can only have one invoice."
            ]);
        }

        if ($query->picking_status == 'INVOICED') {
            return response()->json(['status' => false, 'msg' => "Already SI created for this transaction"]);
        }
        
        // ==========================================
        // COLLECT ALL ORDER IDS (Parent + Children)
        // ==========================================
        $allOrderIds = [$query->id];
        $childOrders = collect([]);
        $isParentOrder = $query->isParentOrder();
        
        if ($isParentOrder) {
            $childOrders = $query->getChildOrders();
            if ($childOrders && $childOrders->isNotEmpty()) {
                $allOrderIds = array_merge($allOrderIds, $childOrders->pluck('id')->toArray());
            }
        }
        
        try {
            DB::beginTransaction();
            $newValue = (new TransactionUtil())->getInvoiceNumber($business_id, 'final', $query->location_id, null, null);
            $paymentData = TransactionPayment::where('transaction_id', $query->id)->first();
            
            // Calculate total from all orders (parent + children) for payment status
            $totalOrderAmount = $query->final_total;
            if ($childOrders->isNotEmpty()) {
                $totalOrderAmount += $childOrders->sum('final_total');
            }
            
            $transaction = DB::table('transactions')->insertGetId([
                'business_id' => $query->business_id ?? 1,
                'location_id' => $query->location_id ?? 1,
                'contact_id' => $query->contact_id,
                'type' => 'sell',
                "status" => "final",
                'payment_status' => ($paymentData && $paymentData->amount >= $totalOrderAmount) ? "paid" : "due",
                "customer_group_id" => $query->customer_group_id,
                "invoice_no" => $newValue,
                "total_before_tax" => '',
                "discount_type" => 'percentage', //currently doscount not done 
                'transaction_date' => now(), // payment response
                'final_total' => $final_total,
                'shipping_address' => $query->shipping_address,
                'is_direct_sale' => 1,
                'selling_price_group_id' => $query->selling_price_group_id,
                'recur_interval' => 1.000,
                'recur_interval_type' => 'days',
                'recur_repetitions' => 0,
                'sales_order_ids' => json_encode($allOrderIds), // Include all order IDs
                'packing_charge' => 0.0000,
                "shipping_charges" => 0.00,
                'additional_notes' => 'Web Order',
                'created_by' => auth()->user()->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
                //shipping address
                'shipping_first_name' => $query->shipping_first_name ?? null,
                'shipping_last_name' => $query->shipping_last_name ?? null,
                'shipping_company' => $query->shipping_company ?? null,
                'shipping_address1' => $query->shipping_address1 ?? null,
                'shipping_address2' => $query->shipping_address2 ?? null,
                'shipping_city' => $query->shipping_city ?? null,
                'shipping_state' => $query->shipping_state ?? null,
                'shipping_zip' => $query->shipping_zip ?? null,
                'shipping_country' => $query->shipping_country ?? 'US',
            ]);

            // ==========================================
            // PROCESS SELL LINES FROM ALL ORDERS
            // ==========================================
            $sellLinesData = [];
            $allSellLineIds = [];
            
            // Helper function to process sell lines
            $processLine = function($line, $fulfillmentType, $sourceOrderId) use (&$sellLinesData, &$allSellLineIds, &$final_total, &$totalBeforeTax, $transaction, $allow_overselling) {
                $givenQuantity = $line->picked_quantity ?? $line->quantity;
                
                // For vendor/dropship orders, use quantity directly (no picking/verification)
                if (in_array($fulfillmentType, ['wp_sales_order', 'erp_dropship_order'])) {
                    $givenQuantity = $line->quantity;
                } else {
                    // ERP fulfilled - use picked/verified quantities
                    if ($line->picked_quantity == $line->verified_qty) {
                        $givenQuantity = $line->picked_quantity;
                    } else if ($line->isVerified) {
                        $givenQuantity = min($line->picked_quantity, $line->verified_qty);
                    }
                }
                
                $sellLinesData[] = [
                    'transaction_id' => $transaction,
                    'product_id' => $line->product_id,
                    'variation_id' => $line->variation_id,
                    'quantity' => $givenQuantity,
                    'ordered_quantity' => $line->ordered_quantity ?? $line->quantity,
                    'unit_price' => $line->unit_price,
                    'unit_price_before_discount' => $line->unit_price_before_discount ?? null,
                    'unit_price_inc_tax' => $line->unit_price_inc_tax ?? null,
                    'item_tax' => $line->item_tax,
                    'so_line_id' => $line->id,
                    'line_discount_type' => $line->line_discount_type ?? 'fixed',
                    'line_discount_amount' => $line->line_discount_amount ?? 0,
                    // Store fulfillment type for color coding in invoice
                    'sell_line_note' => json_encode([
                        'fulfillment_type' => $fulfillmentType,
                        'source_order_id' => $sourceOrderId
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $totalBeforeTax += $line->unit_price * $givenQuantity;
                $itemSub = ($line->unit_price_inc_tax ?? $line->unit_price) * $givenQuantity;
                $final_total += $itemSub;
                
                $allSellLineIds[] = $line->id;
                
                // Update the original line
                $line->so_quantity_invoiced = $givenQuantity;
                $line->save();
            };
            
            // ==========================================
            // PROCESS PARENT ORDER'S SELL LINES
            // For non-split orders or remaining parent items
            // ==========================================
            foreach ($query->sell_lines as $line) {
                $givenQuantity = $line->picked_quantity ?? $line->quantity;
                
                if($line->picked_quantity == $line->verified_qty){
                    $givenQuantity = $line->picked_quantity;
                } else {
                    if($line->isVerified){
                        $diff = 0;
                        if($line->verified_qty < $line->picked_quantity){
                            $diff = $line->picked_quantity - $line->verified_qty;
                            $givenQuantity = $line->verified_qty;
                        } else if($line->verified_qty > $line->picked_quantity){
                            $diff = $line->picked_quantity - $line->verified_qty;
                            $givenQuantity = $line->picked_quantity;
                        }
                        if($diff !== 0){
                            $variation = Variation::with('variation_location_details')
                            ->where('id', $line->variation_id)
                            ->first();

                            if (!$variation) {
                                return response()->json(['status' => false, 'message' => 'Product variation not found for line ID ' . $line->id]);
                            }
                            if ($allow_overselling) {
                                $location = $variation->variation_location_details->first();
                            } else {
                                $location = $variation->variation_location_details->firstWhere('qty_available', '>=', 0);
                                if(!$location){
                                    Log::warning('Case failure at SellController.php Log 1811' . $line->id);
                                    $location = $variation->variation_location_details->first();
                                }
                            }
                            if($location){
                                if($diff>0){ 
                                    $location->qty_available += $diff;
                                    $location->save();
                                } else {
                                    $location->qty_available -= $diff;
                                    $location->save();
                                }
                            } else {
                                return response()->json(['status' => false, 'message' => 'Stock Line Not found for ' . $line->variation->name . ' ' . $line->variation->sub_sku]);
                            }
                        }
                    } else {
                        // For parent orders that have been split, sell_lines might be empty or have different verification status
                        if (!$isParentOrder) {
                            return response()->json(['status' => false, 'message' => 'Due to Mismatched Order fulfillment, Sales Order not invoiced.']);
                        }
                        $givenQuantity = $line->quantity; // Use original quantity for split parent
                    }
                }
                
                $sellLinesData[] = [
                    'transaction_id' => $transaction,
                    'product_id' => $line->product_id,
                    'variation_id' => $line->variation_id,
                    'quantity' => $givenQuantity,
                    'ordered_quantity' => $line->ordered_quantity ?? $line->quantity,
                    'unit_price' => $line->unit_price,
                    'unit_price_before_discount' => $line->unit_price_before_discount ?? null,
                    'unit_price_inc_tax' => $line->unit_price_inc_tax ?? null,
                    'item_tax' => $line->item_tax,
                    'so_line_id' => $line->id,
                    'line_discount_type' => $line->line_discount_type ?? 'fixed',
                    'line_discount_amount' => $line->line_discount_amount ?? 0,
                    // Mark as parent/ERP fulfilled
                    'sell_line_note' => json_encode([
                        'fulfillment_type' => 'erp_fulfilled',
                        'source_order_id' => $query->id,
                        'source_invoice_no' => $query->invoice_no
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $totalBeforeTax += $line->unit_price * $givenQuantity;
                $itemSub = ($line->unit_price_inc_tax ?? $line->unit_price) * $givenQuantity;
                $final_total += $itemSub;
                $allSellLineIds[] = $line->id;
                
                $line->so_quantity_invoiced = $givenQuantity;
                $line->save();
            }
            
            // ==========================================
            // PROCESS CHILD ORDER'S SELL LINES
            // Include items from all child orders (ERP, WooCommerce, Vendor Dropship)
            // ==========================================
            if ($isParentOrder && $childOrders->isNotEmpty()) {
                foreach ($childOrders as $childOrder) {
                    $fulfillmentType = $childOrder->type; // erp_sales_order, wp_sales_order, erp_dropship_order
                    
                    foreach ($childOrder->sell_lines as $line) {
                        // For vendor/dropship orders, use quantity directly (vendors handle their own fulfillment)
                        if (in_array($fulfillmentType, ['wp_sales_order', 'erp_dropship_order'])) {
                            $givenQuantity = $line->quantity;
                        } else {
                            // For ERP child orders, use picked/verified quantities
                            $givenQuantity = $line->picked_quantity ?? $line->quantity;
                            if ($line->picked_quantity && $line->verified_qty) {
                                $givenQuantity = min($line->picked_quantity, $line->verified_qty);
                            }
                        }
                        
                        $sellLinesData[] = [
                            'transaction_id' => $transaction,
                            'product_id' => $line->product_id,
                            'variation_id' => $line->variation_id,
                            'quantity' => $givenQuantity,
                            'ordered_quantity' => $line->ordered_quantity ?? $line->quantity,
                            'unit_price' => $line->unit_price,
                            'unit_price_before_discount' => $line->unit_price_before_discount ?? null,
                            'unit_price_inc_tax' => $line->unit_price_inc_tax ?? null,
                            'item_tax' => $line->item_tax,
                            'so_line_id' => $line->id,
                            'line_discount_type' => $line->line_discount_type ?? 'fixed',
                            'line_discount_amount' => $line->line_discount_amount ?? 0,
                            // Mark fulfillment type for color coding
                            'sell_line_note' => json_encode([
                                'fulfillment_type' => $fulfillmentType,
                                'source_order_id' => $childOrder->id,
                                'source_invoice_no' => $childOrder->invoice_no
                            ]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        
                        $totalBeforeTax += $line->unit_price * $givenQuantity;
                        $itemSub = ($line->unit_price_inc_tax ?? $line->unit_price) * $givenQuantity;
                        $final_total += $itemSub;
                        $allSellLineIds[] = $line->id;
                        
                        $line->so_quantity_invoiced = $givenQuantity;
                        $line->save();
                    }
                    
                    // Mark child order as invoiced
                    $childOrder->picking_status = 'INVOICED';
                    $childOrder->save();
                }
            }

            // Update stock for ERP fulfilled items only
            DB::table('variation_location_details')
                ->join('transaction_sell_lines', function ($join) use ($query) {
                    $join->on('variation_location_details.product_id', '=', 'transaction_sell_lines.product_id')
                        ->on('variation_location_details.variation_id', '=', 'transaction_sell_lines.variation_id');
                })
                ->whereIn('transaction_sell_lines.id', $query->sell_lines->pluck('id'))
                ->update([
                    'variation_location_details.in_stock_qty' => DB::raw('variation_location_details.in_stock_qty + (transaction_sell_lines.ordered_quantity - COALESCE(transaction_sell_lines.verified_qty, transaction_sell_lines.quantity))')
                ]);

            // Insert all sell lines (from parent + children)
            if (!empty($sellLinesData)) {
                DB::table('transaction_sell_lines')->insert($sellLinesData);
            }

            // adjust stock of PR 
            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
            $business = [
                        'id' => $business_id,
                        'accounting_method' => $request->session()->get('business.accounting_method'),
                        'location_id' => 1,
                        'pos_settings' => $pos_settings,
            ];
            $transactionMap = Transaction::with('sell_lines')->where('id',$transaction)->first();
            $this->transactionUtil->mapPurchaseSell($business, $transactionMap->sell_lines, 'purchase');

            // make shipment 
            $shipingCharges = 0;
            $shipping_label = '';
            $shipment = $request->input('shipment');


             if (is_array($shipment) && isset($shipment['shipment_type']) && $shipment['shipment_type'] == 'own'){

                // return response()->json($request->warehouse_id);
                $ship = new ShipStationController();
                $shipmentResponse = $ship->createShipmentAndLable($request, $newValue);
                Log::info('Raw Shipment Response:', ['response' => $shipmentResponse]);
                if (is_string($shipmentResponse)) {
                    $shipmentResponse = json_decode($shipmentResponse, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::error('Failed to decode JSON response: ' . json_last_error_msg());
                        return response()->json(['status' => false, 'message' => 'Invalid response format.']);
                    }
                }
                if (!is_array($shipmentResponse)) {
                    Log::error('Unexpected response format: ' . gettype($shipmentResponse));
                    return response()->json(['status' => false, 'message' => 'Unexpected response format.']);
                }
                if (isset($shipmentResponse['status']) && $shipmentResponse['status'] === false) {
                    return response()->json($shipmentResponse);
                }
                // dd($shipmentResponse);
                $shipmentData = $shipmentResponse;
                $shipingCharges = $shipmentData['shipment_cost']['amount'] ?? 0;
                $shipmentDetails = [
                    'warehouse_id' => $request->input('warehouse_id'),
                    'shipment_id' => $shipmentData['label_id'] ?? null,
                    'shipment_cost' => [
                        'currency' => $shipmentData['shipment_cost']['currency'] ?? 'usd',
                        'amount' => $shipmentData['shipment_cost']['amount'] ?? 0
                    ],
                    'carrier_id' => $shipmentData['carrier_id'] ?? null,
                    'service_code' => $shipmentData['service_code'] ?? null,
                    'package_code' => $shipmentData['package_code'] ?? null,
                    'label_download' => $shipmentData['label_download'] ?? [],
                    'tracking_number' => $shipmentData['tracking_number'] ?? null,
                    'tracking_url' => $shipmentData['tracking_url'] ?? null,
                    'carrier_name' => $shipmentData['carrier_code'] ?? null,
                    "delivery_date" => $shipmentData['ship_date'] ?? null,
                ];
                $shipping_label = $shipmentData['label_download'] ?? [];
                if ($shipmentDetails['tracking_url'] == null) {
                    return response()->json(['status' => false, 'message' => $shipmentResponse]);
                }
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

                $updateData = [
                    'shipment' => $shipmentJson,
                    'shipping_status' => 'shipped',
                    'shipping_details' => $shipmentData['tracking_number'] ?? null
                ];
                DB::table('transactions')->where('id', $transaction)->update($updateData);
                
                // Automatically create tracking status when order is shipped
                try {
                    \App\Models\OrderTrackingStatus::updateOrCreate(
                        [
                            'transaction_id' => $query->id,
                            'status' => 'shipped',
                        ],
                        [
                            'status_date' => now(),
                        ]
                    );
                    \Log::info('Tracking status created: shipped', ['transaction_id' => $query->id, 'invoice_id' => $transaction]);
                } catch (\Exception $trackingError) {
                    \Log::error('Failed to create shipped tracking status', [
                        'transaction_id' => $query->id,
                        'invoice_id' => $transaction,
                        'error' => $trackingError->getMessage()
                    ]);
                    // Don't fail the whole operation if tracking fails
                }
                
                // Send notification to customer when order is shipped
                try {
                    Log::info('Attempting to send order shipped notification (own)', [
                        'transaction_id' => $query->id,
                        'invoice_id' => $transaction,
                        'contact_id' => $query->contact_id,
                        'business_id' => $business_id
                    ]);
                    
                    $contact = Contact::find($query->contact_id);
                    if ($contact) {
                        Log::info('Contact found for order shipped notification', [
                            'transaction_id' => $query->id,
                            'invoice_id' => $transaction,
                            'contact_id' => $contact->id,
                            'contact_email' => $contact->email ?? 'no email',
                            'contact_mobile' => $contact->mobile ?? 'no mobile'
                        ]);
                        
                        // Use the invoice transaction for notification
                        $invoiceTransaction = Transaction::find($transaction);
                        if ($invoiceTransaction) {
                            Log::info('Invoice transaction found, dispatching notification', [
                                'transaction_id' => $query->id,
                                'invoice_id' => $transaction
                            ]);
                            
                            SendNotificationJob::dispatch(
                                false, // is_custom = false
                                $business_id,
                                'order_shipped', // Using order_shipped notification type
                                null, // user
                                $contact,
                                $invoiceTransaction // Use invoice transaction for notification
                            );
                            Log::info('Order shipped notification queued successfully (own)', [
                                'transaction_id' => $query->id,
                                'invoice_id' => $transaction,
                                'contact_id' => $contact->id,
                                'business_id' => $business_id
                            ]);
                            
                            // Send Firebase push notification for SHIPPED status
                            $notificationUtil = new \App\Utils\NotificationUtil();
                            $notificationUtil->sendPushNotification(
                                'Order Shipped',
                                'Your order #' . $query->invoice_no . ' has been shipped and is on its way to you!',
                                $query->contact_id,
                                [
                                    'order_id' => $query->id,
                                    'invoice_no' => $query->invoice_no,
                                    'status' => 'shipped',
                                    'tracking_number' => $shipmentData['tracking_number'] ?? null,
                                    'type' => 'order_status_update'
                                ],
                                'non_urgent'
                            );
                        } else {
                            Log::warning('Invoice transaction not found for order shipped notification', [
                                'transaction_id' => $query->id,
                                'invoice_id' => $transaction
                            ]);
                        }
                    } else {
                        Log::warning('Contact not found for order shipped notification (own)', [
                            'transaction_id' => $query->id,
                            'invoice_id' => $transaction,
                            'contact_id' => $query->contact_id
                        ]);
                    }
                } catch (\Exception $notificationError) {
                    Log::error('Failed to queue order shipped notification (own)', [
                        'transaction_id' => $query->id,
                        'invoice_id' => $transaction,
                        'contact_id' => $query->contact_id ?? null,
                        'error' => $notificationError->getMessage(),
                        'file' => $notificationError->getFile(),
                        'line' => $notificationError->getLine(),
                        'trace' => $notificationError->getTraceAsString()
                    ]);
                    // Don't fail the whole operation if notification fails
                }
            }else if (is_array($shipment) && isset($shipment['shipment_type']) && $shipment['shipment_type'] == 'manual'){
                $shipmentData = $request->shipment;
                $shipingCharges = $shipmentData['shipping_charges'] ?? 0;
                $now = now(); // Laravel helper, returns current datetime
                $trackingNumber = $now->format('YmdHis');
                $app_url=config('app.url');
                $shipment_labal_url=$app_url.'/download-shipment-label/'.$transaction.'/pdf';
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
                // return response()->json($shipmentPayload); 

                $updateData = [
                    'shipment' => $shipmentJson,
                    'shipping_status' => 'shipped',
                    'shipping_details' => $shipmentData['tracking_number'] ?? null
                ];
                DB::table('transactions')->where('id', $transaction)->update($updateData);
                
                // Automatically create tracking status when order is shipped
                try {
                    \App\Models\OrderTrackingStatus::updateOrCreate(
                        [
                            'transaction_id' => $query->id,
                            'status' => 'shipped',
                        ],
                        [
                            'status_date' => now(),
                        ]
                    );
                    \Log::info('Tracking status created: shipped', ['transaction_id' => $query->id, 'invoice_id' => $transaction]);
                } catch (\Exception $trackingError) {
                    \Log::error('Failed to create shipped tracking status', [
                        'transaction_id' => $query->id,
                        'invoice_id' => $transaction,
                        'error' => $trackingError->getMessage()
                    ]);
                    // Don't fail the whole operation if tracking fails
                }
                
                // Send notification to customer when order is shipped
                try {
                    Log::info('Attempting to send order shipped notification (manual)', [
                        'transaction_id' => $query->id,
                        'invoice_id' => $transaction,
                        'contact_id' => $query->contact_id,
                        'business_id' => $business_id
                    ]);
                    
                    $contact = Contact::find($query->contact_id);
                    if ($contact) {
                        Log::info('Contact found for order shipped notification (manual)', [
                            'transaction_id' => $query->id,
                            'invoice_id' => $transaction,
                            'contact_id' => $contact->id,
                            'contact_email' => $contact->email ?? 'no email',
                            'contact_mobile' => $contact->mobile ?? 'no mobile'
                        ]);
                        
                        // Use the invoice transaction for notification
                        $invoiceTransaction = Transaction::find($transaction);
                        if ($invoiceTransaction) {
                            Log::info('Invoice transaction found, dispatching notification (manual)', [
                                'transaction_id' => $query->id,
                                'invoice_id' => $transaction
                            ]);
                            
                            SendNotificationJob::dispatch(
                                false, // is_custom = false
                                $business_id,
                                'order_shipped', // Using order_shipped notification type
                                null, // user
                                $contact,
                                $invoiceTransaction // Use invoice transaction for notification
                            );
                            Log::info('Order shipped notification queued successfully (manual)', [
                                'transaction_id' => $query->id,
                                'invoice_id' => $transaction,
                                'contact_id' => $contact->id,
                                'business_id' => $business_id
                            ]);
                            
                            // Send Firebase push notification for SHIPPED status
                            $notificationUtil = new \App\Utils\NotificationUtil();
                            $notificationUtil->sendPushNotification(
                                'Order Shipped',
                                'Your order #' . $query->invoice_no . ' has been shipped and is on its way to you!',
                                $query->contact_id,
                                [
                                    'order_id' => $query->id,
                                    'invoice_no' => $query->invoice_no,
                                    'status' => 'shipped',
                                    'tracking_number' => $trackingNumber,
                                    'type' => 'order_status_update'
                                ],
                                'non_urgent'
                            );
                        } else {
                            Log::warning('Invoice transaction not found for order shipped notification (manual)', [
                                'transaction_id' => $query->id,
                                'invoice_id' => $transaction
                            ]);
                        }
                    } else {
                        Log::warning('Contact not found for order shipped notification (manual)', [
                            'transaction_id' => $query->id,
                            'invoice_id' => $transaction,
                            'contact_id' => $query->contact_id
                        ]);
                    }
                } catch (\Exception $notificationError) {
                    Log::error('Failed to queue order shipped notification (manual)', [
                        'transaction_id' => $query->id,
                        'invoice_id' => $transaction,
                        'contact_id' => $query->contact_id ?? null,
                        'error' => $notificationError->getMessage(),
                        'file' => $notificationError->getFile(),
                        'line' => $notificationError->getLine(),
                        'trace' => $notificationError->getTraceAsString()
                    ]);
                    // Don't fail the whole operation if notification fails
                }
            }
            //fix due of customer 
            $due = "due";
            if ($paymentData && $paymentData->amount > 0) {
                $returnAmount = $paymentData->amount - ($final_total + $shipingCharges);
                $msg = "";
                if ($returnAmount > 0) {
                    $customer = Contact::find($query->contact_id);
                    $customer->balance += $returnAmount;
                    $customer->save();
                    $msg = "Return Amount: " . $returnAmount . " has been added from " . $paymentData->amount;
                    $due = 'paid';
                } else if ($returnAmount < 0) {
                    $returnAmount = abs($returnAmount);
                    $msg = "Due Amount: " . $returnAmount . " has been counted";
                    $due = 'partial';
                }
                // create payment line
                if ($due == 'paid') {
                    $paymentData->amount = $paymentData->amount - $returnAmount;
                    $msg .= " (wallet increased)";
                } else {
                    $paymentData->amount = $paymentData->amount;
                }
                $msg .= "(" . $paymentData->payment_ref_no . " to " . $newValue . " )";
                $paymentData->transaction_id = $transaction;
                $paymentData->payment_ref_no = $newValue;
                $paymentData->note = $msg;
                $paymentData->updated_at = now();
                $paymentData->save();
            } else {
                // don not create 0 amount transaction 
            }
            DB::table('transactions')->where('id', $transaction)->update([
                'final_total' => $final_total + $shipingCharges,
                'payment_status' => $due,
                'shipping_charges' => $shipingCharges,
                'total_before_tax' => $totalBeforeTax,
                'updated_at' => now(),
            ]);
            $query->picking_status = 'INVOICED';
            $query->status = 'completed';
            $query->save();

            // When status changes from packed to completed, mark as shipped and send notification
            // Similar to how picking -> picked triggers order_packed email
            Log::info('Order status changed to completed - checking for shipped notification', [
                'transaction_id' => $query->id,
                'invoice_no' => $query->invoice_no,
                'picking_status' => $query->picking_status,
                'status' => $query->status,
                'shipping_status' => $query->shipping_status,
                'business_id' => $business_id
            ]);
            
            try {
                // Set shipping_status to shipped if not already set
                if ($query->shipping_status !== 'shipped') {
                    $query->shipping_status = 'shipped';
                    $query->save();
                    
                    Log::info('Shipping status updated to shipped when order completed', [
                        'transaction_id' => $query->id,
                        'invoice_no' => $query->invoice_no,
                        'business_id' => $business_id
                    ]);
                } else {
                    Log::info('Shipping status already set to shipped', [
                        'transaction_id' => $query->id,
                        'invoice_no' => $query->invoice_no
                    ]);
                }
                
                // Create tracking status for shipped
                try {
                    \App\Models\OrderTrackingStatus::updateOrCreate(
                        [
                            'transaction_id' => $query->id,
                            'status' => 'shipped',
                        ],
                        [
                            'status_date' => now(),
                            'updated_by' => auth()->user()->id ?? null,
                        ]
                    );
                    Log::info('Tracking status created: shipped (when order completed)', [
                        'transaction_id' => $query->id,
                        'invoice_no' => $query->invoice_no
                    ]);
                } catch (\Exception $trackingError) {
                    Log::error('Failed to create shipped tracking status', [
                        'transaction_id' => $query->id,
                        'error' => $trackingError->getMessage()
                    ]);
                }
                
                // Send email notification (synchronous, like packed status)
                $contact = Contact::find($query->contact_id);
                if ($contact && !empty($contact->email)) {
                    Log::info('Attempting to send order shipped notification when status completed', [
                        'transaction_id' => $query->id,
                        'invoice_no' => $query->invoice_no,
                        'contact_id' => $contact->id,
                        'contact_email' => $contact->email,
                        'contact_mobile' => $contact->mobile ?? 'no mobile',
                        'business_id' => $business_id
                    ]);
                    
                    $notificationUtil = new NotificationUtil();
                    $notificationUtil->autoSendNotification(
                        $business_id,
                        'order_shipped',
                        $query, // Use the original order transaction
                        $contact
                    );
                    
                    Log::info('Order shipped notification sent successfully (when status completed)', [
                        'transaction_id' => $query->id,
                        'invoice_no' => $query->invoice_no,
                        'contact_id' => $contact->id,
                        'business_id' => $business_id
                    ]);
                    
                    // Send Firebase push notification for COMPLETED status
                    $notificationUtil = new \App\Utils\NotificationUtil();
                    $notificationUtil->sendPushNotification(
                        'Order Completed',
                        'Your order #' . $query->invoice_no . ' has been completed and is on its way to you!',
                        $query->contact_id,
                        [
                            'order_id' => $query->id,
                            'invoice_no' => $query->invoice_no,
                            'status' => 'completed',
                            'type' => 'order_status_update'
                        ],
                        'non_urgent'
                    );
                } else {
                    Log::warning('Contact not found or no email for order shipped notification', [
                        'transaction_id' => $query->id,
                        'invoice_no' => $query->invoice_no,
                        'contact_id' => $query->contact_id,
                        'contact_found' => $contact ? 'yes' : 'no',
                        'has_email' => $contact ? (!empty($contact->email) ? 'yes' : 'no') : 'N/A'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to process order shipped notification when status completed', [
                    'transaction_id' => $query->id,
                    'invoice_no' => $query->invoice_no ?? 'N/A',
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail the whole operation if notification fails
            }

            // DB::rollBack();
            DB::commit();
            
            // ==========================================
            // GENERATE GIFT CARDS FOR GIFT CARD PRODUCTS
            // ==========================================
            try {
                $this->generateGiftCardsForInvoice($transaction, $query, $business_id);
            } catch (\Exception $giftCardError) {
                Log::error('Failed to generate gift cards for invoice', [
                    'transaction_id' => $transaction,
                    'order_id' => $query->id,
                    'invoice_no' => $newValue,
                    'error' => $giftCardError->getMessage(),
                    'file' => $giftCardError->getFile(),
                    'line' => $giftCardError->getLine()
                ]);
                // Don't fail the invoice creation if gift card generation fails
            }
            // $output = [
            //     'success' => 1,
            //     'msg' => "Invoice- {$newValue} Created successfully",
            // ];
            // return redirect("/sells/{$transaction}/edit")->with('status', $output);
            if ($request->input('shipment')) {

                return response()->json(['status' => true, 'msg' => "Invoice- {$newValue} Created successfully", "shipping_label" => $shipping_label, 'transaction' => $transaction]);
            } else {
                return response()->json(['status' => true, 'msg' => "Invoice- {$newValue} Created successfully", "shipping_label" => $shipping_label, 'transaction' => $transaction]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'An error occurred while processing the request: ' . $e->getMessage() . ' ' . $e->getLine()]);
        }
    }

    public function createSaleRedirect(Request $request, $id, $type)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $saleRep = request()->session()->get('user.id');
        $final_total = 0;
        $payedAmount = 0;
        $location_id = 1; //'need to make dynamic'
        $customer =  Contact::where('business_id', $business_id)->where('id', $id)->first();
        try {
            DB::beginTransaction();
            if ($type == 'so') {
                $newValue = (new TransactionUtil())->getInvoiceNumber($business_id, null, $location_id, null, 'sales_order');
                $transaction = DB::table('transactions')->insertGetId([
                    'business_id' => $query->business_id ?? 1,
                    'location_id' => $query->location_id ?? 1,
                    'contact_id' => $id,
                    'type' => 'sales_order',
                    "status" => "ordered",
                    'payment_status' => null,
                    "customer_group_id" => $customer->customer_group_id,
                    "invoice_no" => $newValue,
                    "total_before_tax" => '',
                    "discount_type" => 'percentage',
                    'transaction_date' => now(),
                    'final_total' => $final_total,
                    'shipping_address' => $customer->shipping_address,
                    'is_direct_sale' => 1,
                    'selling_price_group_id' => $customer->customer_group_id,
                    'recur_interval' => 1.000,
                    'recur_interval_type' => 'days',
                    'recur_repetitions' => 0,
                    'sales_order_ids' => null,
                    'packing_charge' => 0.0000,
                    "shipping_charges" => 0.00,
                    'additional_notes' => 'Acount Order',
                    'created_by' => $saleRep,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $newValue = (new TransactionUtil())->getInvoiceNumber($business_id, 'final', $location_id, null, null);
                $transaction = DB::table('transactions')->insertGetId([
                    'business_id' => $query->business_id ?? 1,
                    'location_id' => $query->location_id ?? 1,
                    'contact_id' => $id,
                    'type' => 'sell',
                    "status" => "draft",
                    'payment_status' => null,
                    "customer_group_id" => $id,
                    "invoice_no" => null,
                    "total_before_tax" => '',
                    "discount_type" => 'percentage',
                    'transaction_date' => now(),
                    'final_total' => $final_total,
                    'shipping_address' => $customer->shipping_address,
                    'is_direct_sale' => 1,
                    'selling_price_group_id' => $customer->customer_group_id,
                    'recur_interval' => 1.000,
                    'recur_interval_type' => 'days',
                    'recur_repetitions' => 0,
                    'sales_order_ids' => null,
                    'packing_charge' => 0.0000,
                    "shipping_charges" => 0.00,
                    'additional_notes' => 'Acount Order',
                    'created_by' => $saleRep, //admin
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'redirect_url' => url("/sells/{$transaction}/edit"),
                'message' => 'Opening sale'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'An error occurred while processing the request: ' . $e->getMessage()]);
        }
    }
    public function cancelSO(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission([
            'sell.view',
            'sell.create',
            'direct_sell.access',
            'direct_sell.view',
            'view_own_sell_only',
            'view_commission_agent_sell',
            'access_shipping',
            'access_own_shipping',
            'access_commission_agent_shipping',
            'so.view_all',
            'so.view_own'
        ])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action'], 403);
        }

        $business_id = request()->session()->get('user.business_id');

        DB::beginTransaction();
        try {
            $transaction = Transaction::with('payment_lines')->where('type', 'sales_order')
                ->where('business_id', $business_id)
                ->find($id);
            // session lock 
            $isLockModal = false;
            $orderFulfillmentController = app(OrderfulfillmentController::class);
            $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $transaction->id,true);
            if($lockModal['status'] == false){
                return response()->json(['status' => false, 'message' => $lockModal['message']]);
            }
            if (!$transaction) {
                return response()->json(['status' => false, 'message' => 'Transaction not found']);
            }
            if ($transaction->status === 'cancelled') {
                return response()->json(['status' => false, 'message' => 'Order is already cancelled']);
            }
            foreach ($transaction->sell_lines as $sellLine) {
                $variation = Variation::with('variation_location_details', 'product')
                    ->where('id', $sellLine->variation_id)
                    ->first();

                if ($variation) {
                    $product = $variation->product;
                    $enable_stock = $product ? ($product->enable_stock ?? 1) : 1;
                    
                    // Only manage stock if enable_stock == 1
                    if ($enable_stock == 1) {
                        $location = $variation->variation_location_details->first();
                        if ($location) {
                            // Restore hand stock for picked quantity
                            $location->qty_available += $sellLine->picked_quantity;
                            
                            // Calculate remaining ordered quantities from other sales orders
                            $remaining_ordered = DB::table('transaction_sell_lines as tsl')
                                ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
                                ->where('t.type', 'sales_order')
                                ->where('t.status', '!=', 'cancelled')
                                ->where('tsl.variation_id', $sellLine->variation_id)
                                ->where('t.id', '!=', $transaction->id)
                                ->sum('tsl.ordered_quantity');
                            
                            // Calculate virtual stock correctly
                            if ($location->qty_available < 0) {
                                // When hand stock is negative, virtual stock should be 0
                                $location->in_stock_qty = 0;
                            } else {
                                // When hand stock is positive, virtual stock is available minus remaining orders
                                $location->in_stock_qty = max(0, $location->qty_available - $remaining_ordered);
                            }
                            
                            $location->save();
                        }
                    }
                }
                $sellLine->picked_quantity = 0;
                $sellLine->save();
            }
            $amount = 0;
            foreach ($transaction->payment_lines as $paymentLine) {
                $amount += $paymentLine->amount;
            }
            $contact = Contact::find($transaction->contact_id);
            $contact->balance += $amount;
            $contact->save();
            $transaction->status = 'cancelled';
            $transaction->picking_status = null;
            $transaction->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Order cancelled and stock restored']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function cancelSOFromPending(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission([
            'sell.view',
            'sell.create',
            'direct_sell.access',
            'direct_sell.view',
            'view_own_sell_only',
            'view_commission_agent_sell',
            'access_shipping',
            'access_own_shipping',
            'access_commission_agent_shipping',
            'so.view_all',
            'so.view_own'
        ])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action'], 403);
        }

        $business_id = request()->session()->get('user.business_id');
        $ids = $request->ids;
        $count = 0;
        foreach ($ids as $id) {
            try {
                $transaction = Transaction::with('payment_lines')->where('type', 'sales_order')
                    ->where('business_id', $business_id)
                    ->find($id);
                $transction_status = $transaction->picking_status;
                // Get business settings for overselling
                $business = Business::find($business_id);
                $pos_settings = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);
                $allow_overselling = !empty($pos_settings['allow_overselling']) ? true : false;

               
                if (!$transaction) {
                    return response()->json(['status' => false, 'message' => 'Transaction not found']);
                }
                if ($transaction->status === 'cancelled') {
                    return response()->json(['status' => false, 'message' => 'Order is already cancelled']);
                }
                foreach ($transaction->sell_lines as $sellLine) {
                    $variation = Variation::with('variation_location_details', 'product')
                        ->where('id', $sellLine->variation_id)
                        ->first();

                    if ($variation) {
                        $product = $variation->product;
                        $enable_stock = $product ? ($product->enable_stock ?? 1) : 1;
                        
                        // Only manage stock if enable_stock == 1
                        if ($enable_stock == 1) {
                            if ($allow_overselling) {
                                $location = $variation->variation_location_details->first(); // $details = $location variable 
                            } else {
                                $location = $variation->variation_location_details->firstWhere('qty_available', '>=', 0);
                                if(!$location){
                                    Log::warning('Case failure at SellController.php Log 2275' . $sellLine->id);
                                    $location = $variation->variation_location_details->first();
                                }
                            }
                            if ($location) {
                                // Restore hand stock for picked quantity
                                if($transction_status === 'PICKED'){
                                    $location->qty_available += $sellLine->picked_quantity;
                                    $sellLine->picked_quantity = 0;
                                    $sellLine->is_picked = 0;
                                    $sellLine->verified_qty = 0;
                                    $sellLine->shorted_picked_qty = 0;
                                    $sellLine->barcode_picked_qty = 0;
                                    $sellLine->manual_picked_qty = 0;
                                    $sellLine->isVerified = 0;
                                    $sellLine->save();
                                }
                                
                                // Calculate remaining ordered quantities from other sales orders
                                $remaining_ordered = DB::table('transaction_sell_lines as tsl')
                                    ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
                                    ->where('t.type', 'sales_order')
                                    ->where('t.status', '!=', 'cancelled')
                                    ->where('tsl.variation_id', $sellLine->variation_id)
                                    ->where('t.id', '!=', $transaction->id)
                                    ->sum('tsl.ordered_quantity');
                                
                                // Calculate virtual stock correctly
                                if ($location->qty_available < 0) {
                                    // When hand stock is negative, virtual stock should be 0
                                    $location->in_stock_qty = 0;
                                } else {
                                    // When hand stock is positive, virtual stock is available minus remaining orders
                                    $location->in_stock_qty = max(0, $location->qty_available - $remaining_ordered);
                                }
                                
                                $location->save();
                            }
                        } else {
                            // If enable_stock == 0, still reset the sell line fields but don't modify stock
                            if($transction_status === 'PICKED'){
                                $sellLine->picked_quantity = 0;
                                $sellLine->is_picked = 0;
                                $sellLine->verified_qty = 0;
                                $sellLine->shorted_picked_qty = 0;
                                $sellLine->barcode_picked_qty = 0;
                                $sellLine->manual_picked_qty = 0;
                                $sellLine->isVerified = 0;
                                $sellLine->save();
                            }
                        }
                    }
                    // Reset picked quantity if not already reset above (when status is not PICKED)
                    if ($transction_status !== 'PICKED') {
                        $sellLine->picked_quantity = 0;
                        $sellLine->save();
                    }
                }
                $amount = 0;
                if(isset($transaction->payment_lines)){
                    foreach ($transaction->payment_lines as $paymentLine) {
                        $amount += $paymentLine->amount;
                    }
                }
                $contact = Contact::find($transaction->contact_id);
                $contact->balance += $amount;
                $contact->save();
                $transaction->status = 'cancelled';
                $transaction->picking_status = null;
                $transaction->isPicked = 0;
                $transaction->isVerified = 0;
                $transaction->pickerID = null;
                $transaction->verifierID = null;
                $transaction->save();
                DB::commit();
                $count++;
                // return response()->json(['status' => true, 'message' => 'Order cancelled and stock restored']);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::emergency($e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
                return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        return response()->json(['status' => true, 'message' => $count . ' Orders cancelled']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('direct_sell.update') && ! auth()->user()->can('so.update')) {
            abort(403, 'Unauthorized action.');
        }

        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (! $this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', [
                    'success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days]),
                ]);
        }


        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', [
                'success' => 0,
                'msg' => __('lang_v1.return_exist'),
            ]);
        }

        $business_id = request()->session()->get('user.business_id');
        $business_details = $this->businessUtil->getDetails($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $transaction = Transaction::where('business_id', $business_id)
            ->with(['price_group', 'types_of_service', 'media', 'media.uploaded_by_user'])
            ->whereIn('type', ['sell', 'sales_order'])
            ->findorfail($id);
        $cid =  $transaction->contact_id;

        if ($cid) {
            $customer = Contact::find($cid);
            if($customer->contact_status=='inactive'){
                return back()->with('status', [
                'success' => 0,
                'msg' => "Customer is deactivated",
            ]);
            }
            
        }
        // session lock 
        $isLockModal = false;
        $orderFulfillmentController = app(OrderfulfillmentController::class);
        $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $id);

        if ($lockModal instanceof \Illuminate\View\View) {
            return $lockModal;
        }


        if ($transaction->type == 'sales_order' && ! auth()->user()->can('so.update')) {
            abort(403, 'Unauthorized action.');
        }

        $location_id = $transaction->location_id;
        $location_printer_type = BusinessLocation::find($location_id)->receipt_printer_type;

        $sell_details = TransactionSellLine::join(
            'products AS p',
            'transaction_sell_lines.product_id',
            '=',
            'p.id'
        )
            ->join(
                'variations AS variations',
                'transaction_sell_lines.variation_id',
                '=',
                'variations.id'
            )
            ->join(
                'product_variations AS pv',
                'variations.product_variation_id',
                '=',
                'pv.id'
            )
            ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', '=', $location_id);
            })
            ->leftjoin('units', 'units.id', '=', 'p.unit_id')
            ->leftjoin('units as u', 'p.secondary_unit_id', '=', 'u.id')
            ->where('transaction_sell_lines.transaction_id', $id)
            ->with(['warranties', 'so_line'])
            ->select(
                DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                'p.id as product_id',
                'p.image as product_image',
                'p.enable_stock',
                'p.name as product_actual_name',
                'p.type as product_type',
                'pv.name as product_variation_name',
                'pv.is_dummy as is_dummy',
                'variations.name as variation_name',
                'variations.sub_sku',
                'p.barcode_type',
                'p.enable_sr_no',
                'p.ml as ml',
                'p.ct as ct',
                'p.locationTaxType as locationTaxType',
                'variations.id as variation_id',
                'units.short_name as unit',
                'units.allow_decimal as unit_allow_decimal',
                'u.short_name as second_unit',
                'transaction_sell_lines.secondary_unit_quantity',
                'transaction_sell_lines.tax_id as tax_id',
                'transaction_sell_lines.item_tax as item_tax',
                'transaction_sell_lines.unit_price as default_sell_price',
                'transaction_sell_lines.unit_price_inc_tax as sell_price_inc_tax',
                'transaction_sell_lines.unit_price_before_discount as unit_price_before_discount',
                'transaction_sell_lines.id as transaction_sell_lines_id',
                'transaction_sell_lines.id',
                'transaction_sell_lines.quantity as quantity_ordered',
                'transaction_sell_lines.sell_line_note as sell_line_note',
                'transaction_sell_lines.parent_sell_line_id',
                'transaction_sell_lines.lot_no_line_id',
                'transaction_sell_lines.line_discount_type',
                'transaction_sell_lines.line_discount_amount',
                'transaction_sell_lines.res_service_staff_id',
                'units.id as unit_id',
                'transaction_sell_lines.sub_unit_id',
                'transaction_sell_lines.so_line_id',
                DB::raw('vld.qty_available + transaction_sell_lines.quantity AS qty_available')
            )
            ->get();

        if (! empty($sell_details)) {
            foreach ($sell_details as $key => $value) {

                $variation = Variation::with('media')->findOrFail($value->variation_id);
                $sell_details[$key]->media = $variation->media;

                //If modifier or combo sell line then unset
                if (! empty($sell_details[$key]->parent_sell_line_id)) {
                    unset($sell_details[$key]);
                } else {
                    if ($transaction->status != 'final') {
                        $actual_qty_avlbl = $value->qty_available - $value->quantity_ordered;
                        $sell_details[$key]->qty_available = $actual_qty_avlbl;
                        $value->qty_available = $actual_qty_avlbl;
                    }

                    $sell_details[$key]->formatted_qty_available = $this->productUtil->num_f($value->qty_available, false, null, true);
                    $lot_numbers = [];
                    if (request()->session()->get('business.enable_lot_number') == 1) {
                        $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($value->variation_id, $business_id, $location_id);
                        foreach ($lot_number_obj as $lot_number) {
                            //If lot number is selected added ordered quantity to lot quantity available
                            if ($value->lot_no_line_id == $lot_number->purchase_line_id) {
                                $lot_number->qty_available += $value->quantity_ordered;
                            }

                            $lot_number->qty_formated = $this->transactionUtil->num_f($lot_number->qty_available);
                            $lot_numbers[] = $lot_number;
                        }
                    }
                    $sell_details[$key]->lot_numbers = $lot_numbers;

                    if (! empty($value->sub_unit_id)) {
                        $value = $this->productUtil->changeSellLineUnit($business_id, $value);
                        $sell_details[$key] = $value;
                    }

                    if ($this->transactionUtil->isModuleEnabled('modifiers')) {
                        //Add modifier details to sel line details
                        $sell_line_modifiers = TransactionSellLine::where('parent_sell_line_id', $sell_details[$key]->transaction_sell_lines_id)
                            ->where('children_type', 'modifier')
                            ->get();
                        $modifiers_ids = [];
                        if (count($sell_line_modifiers) > 0) {
                            $sell_details[$key]->modifiers = $sell_line_modifiers;
                            foreach ($sell_line_modifiers as $sell_line_modifier) {
                                $modifiers_ids[] = $sell_line_modifier->variation_id;
                            }
                        }
                        $sell_details[$key]->modifiers_ids = $modifiers_ids;

                        //add product modifier sets for edit
                        $this_product = Product::find($sell_details[$key]->product_id);
                        if (count($this_product->modifier_sets) > 0) {
                            $sell_details[$key]->product_ms = $this_product->modifier_sets;
                        }
                    }

                    //Get details of combo items
                    if ($sell_details[$key]->product_type == 'combo') {
                        $sell_line_combos = TransactionSellLine::where('parent_sell_line_id', $sell_details[$key]->transaction_sell_lines_id)
                            ->where('children_type', 'combo')
                            ->get()
                            ->toArray();
                        if (! empty($sell_line_combos)) {
                            $sell_details[$key]->combo_products = $sell_line_combos;
                        }

                        //calculate quantity available if combo product
                        $combo_variations = [];
                        foreach ($sell_line_combos as $combo_line) {
                            $combo_variations[] = [
                                'variation_id' => $combo_line['variation_id'],
                                'quantity' => $combo_line['quantity'] / $sell_details[$key]->quantity_ordered,
                                'unit_id' => null,
                            ];
                        }
                        $sell_details[$key]->qty_available =
                            $this->productUtil->calculateComboQuantity($location_id, $combo_variations);

                        if ($transaction->status == 'final') {
                            $sell_details[$key]->qty_available = $sell_details[$key]->qty_available + $sell_details[$key]->quantity_ordered;
                        }

                        $sell_details[$key]->formatted_qty_available = $this->productUtil->num_f($sell_details[$key]->qty_available, false, null, true);
                    }
                }
            }
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
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

        $transaction->transaction_date = $this->transactionUtil->format_date($transaction->transaction_date, true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $waiters = [];
        if ($this->productUtil->isModuleEnabled('service_staff') && ! empty($pos_settings['inline_service_staff'])) {
            $waiters = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $invoice_schemes = [];
        $default_invoice_schemes = null;

        if ($transaction->status == 'draft') {
            $invoice_schemes = InvoiceScheme::forDropdown($business_id);
            $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        }

        $redeem_details = [];
        if (request()->session()->get('business.enable_rp') == 1) {
            $redeem_details = $this->transactionUtil->getRewardRedeemDetails($business_id, $transaction->contact_id);

            $redeem_details['points'] += $transaction->rp_redeemed;
            $redeem_details['points'] -= $transaction->rp_earned;
        }

        $edit_discount = auth()->user()->can('edit_product_discount_from_sale_screen');
        $edit_price = auth()->user()->can('edit_product_price_from_sale_screen');

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = ! empty($common_settings['enable_product_warranty']) ? true : false;
        $warranties = $is_warranty_enabled ? Warranty::forDropdown($business_id) : [];

        $statuses = Transaction::sell_statuses();

        $is_order_request_enabled = false;
        $is_crm = $this->moduleUtil->isModuleInstalled('Crm');
        if ($is_crm) {
            $crm_settings = Business::where('id', auth()->user()->business_id)
                ->value('crm_settings');
            $crm_settings = ! empty($crm_settings) ? json_decode($crm_settings, true) : [];

            if (! empty($crm_settings['enable_order_request'])) {
                $is_order_request_enabled = true;
            }
        }

        $sales_orders = [];
        if (! empty($pos_settings['enable_sales_order']) || $is_order_request_enabled) {
            $sales_orders = Transaction::where('business_id', $business_id)
                ->where('type', 'sales_order')
                ->where('contact_id', $transaction->contact_id)
                ->where(function ($q) use ($transaction) {
                    $q->where('status', '!=', 'completed');

                    if (! empty($transaction->sales_order_ids)) {
                        $q->orWhereIn('id', $transaction->sales_order_ids);
                    }
                })
                ->pluck('invoice_no', 'id');
        }

        $payment_types = $this->transactionUtil->payment_types($transaction->location_id, false, $business_id);

        $payment_lines = $this->transactionUtil->getPaymentDetails($id);
        //If no payment lines found then add dummy payment line.
        if (empty($payment_lines)) {
            $payment_lines[] = $this->dummyPaymentLine;
        }

        $change_return = $this->dummyPaymentLine;

        $customer_due = $this->transactionUtil->getContactDue($transaction->contact_id, $transaction->business_id);

        $customer_due = $customer_due != 0 ? $this->transactionUtil->num_f($customer_due, true) : '';

        //Added check because $users is of no use if enable_contact_assign if false
        $users = config('constants.enable_contact_assign') ? User::forDropdown($business_id, false, false, false, true) : [];

        return view('sell.edit')
            ->with(compact('business_details', 'taxes', 'sell_details', 'transaction', 'commission_agent', 'types', 'customer_groups', 'pos_settings', 'waiters', 'invoice_schemes', 'default_invoice_schemes', 'redeem_details', 'edit_discount', 'edit_price', 'shipping_statuses', 'warranties', 'statuses', 'sales_orders', 'payment_types', 'accounts', 'payment_lines', 'change_return', 'is_order_request_enabled', 'customer_due', 'users', 'isLockModal', 'business_locations'));
    }

    /**
     * Display a listing sell drafts.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDrafts()
    {
        if (! auth()->user()->can('draft.view_all') && ! auth()->user()->can('draft.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, true);

        return view('sale_pos.draft')
            ->with(compact('business_locations', 'customers', 'sales_representative'));
    }

    /**
     * Display a listing sell quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getQuotations()
    {
        if (! auth()->user()->can('quotation.view_all') && ! auth()->user()->can('quotation.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, true);

        return view('sale_pos.quotations')
            ->with(compact('business_locations', 'customers', 'sales_representative'));
    }

    /**
     * Send the datatable response for draft or quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDraftDatables()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $is_quotation = request()->input('is_quotation', 0);

            $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin('transaction_sell_lines as tsl', function ($join) {
                    $join->on('transactions.id', '=', 'tsl.transaction_id')
                        ->whereNull('tsl.parent_sell_line_id');
                })
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'draft')
                ->select(
                    'transactions.id',
                    'transaction_date',
                    'invoice_no',
                    'contacts.name',
                    'contacts.mobile',
                    'contacts.supplier_business_name',
                    'bl.name as business_location',
                    'is_direct_sale',
                    'sub_status',
                    DB::raw('COUNT( DISTINCT tsl.id) as total_items'),
                    DB::raw('SUM(tsl.quantity) as total_quantity'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as added_by"),
                    'transactions.is_export'
                );

            if ($is_quotation == 1) {
                $sells->where('transactions.sub_status', 'quotation');

                if (! auth()->user()->can('quotation.view_all') && auth()->user()->can('quotation.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            } else {
                if (! auth()->user()->can('draft.view_all') && auth()->user()->can('draft.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('transaction_date', '>=', $start)
                    ->whereDate('transaction_date', '<=', $end);
            }

            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            if (! empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }

            if ($is_woocommerce) {
                $sells->addSelect('transactions.woocommerce_order_id');
            }

            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group dropdown scroll-safe-dropdown">
                                <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max dropdown-toggle" 
                                    data-toggle="dropdown" aria-expanded="false">' .
                            __('messages.actions') .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li>
                                    <a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]) . '" class="btn-modal" data-container=".view_modal">
                                        <i class="fas fa-eye" aria-hidden="true"></i>' . __('messages.view') . '
                                    </a>
                                    </li>';

                        if (auth()->user()->can('draft.update') || auth()->user()->can('quotation.update')) {
                            if ($row->is_direct_sale == 1) {
                                $html .= '<li>
                                            <a target="_blank" href="' . action([\App\Http\Controllers\SellController::class, 'edit'], [$row->id]) . '">
                                                <i class="fas fa-edit"></i>' . __('messages.edit') . '
                                            </a>
                                        </li>';
                            } else {
                                $html .= '<li>
                                            <a target="_blank" href="' . action([\App\Http\Controllers\SellPosController::class, 'edit'], [$row->id]) . '">
                                                <i class="fas fa-edit"></i>' . __('messages.edit') . '
                                            </a>
                                        </li>';
                            }
                        }

                        $html .= '<li>
                                    <a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fas fa-print" aria-hidden="true"></i>' . __('messages.print') . '</a>
                                </li>';

                        if (config('constants.enable_download_pdf')) {
                            $sub_status = $row->sub_status == 'proforma' ? 'proforma' : '';
                            $html .= '<li>
                                        <a href="' . route('quotation.downloadPdf', ['id' => $row->id, 'sub_status' => $sub_status]) . '" target="_blank">
                                            <i class="fas fa-print" aria-hidden="true"></i>' . __('lang_v1.download_pdf') . '
                                        </a>
                                    </li>';
                        }

                        if ((auth()->user()->can('sell.create') || auth()->user()->can('direct_sell.access')) && config('constants.enable_convert_draft_to_invoice')) {
                            $html .= '<li>
                                        <a href="' . action([\App\Http\Controllers\SellPosController::class, 'convertToInvoice'], [$row->id]) . '" class="convert-draft"><i class="fas fa-sync-alt"></i>' . __('lang_v1.convert_to_invoice') . '</a>
                                    </li>';
                        }

                        if ($row->sub_status != 'proforma') {
                            $html .= '<li>
                                        <a href="' . action([\App\Http\Controllers\SellPosController::class, 'convertToProforma'], [$row->id]) . '" class="convert-to-proforma"><i class="fas fa-sync-alt"></i>' . __('lang_v1.convert_to_proforma') . '</a>
                                    </li>';
                        }

                        if (auth()->user()->can('draft.delete') || auth()->user()->can('quotation.delete')) {
                            $html .= '<li>
                                <a href="' . action([\App\Http\Controllers\SellPosController::class, 'destroy'], [$row->id]) . '" class="delete-sale"><i class="fas fa-trash"></i>' . __('messages.delete') . '</a>
                                </li>';
                        }

                        if ($row->sub_status == 'quotation') {
                            $html .= '<li>
                                        <a href="' . action([\App\Http\Controllers\SellPosController::class, 'copyQuotation'], [$row->id]) . '" 
                                        class="copy_quotation"><i class="fas fa-copy"></i>' .
                                __("lang_v1.copy_quotation") . '</a>
                                    </li>
                                    <li>
                                        <a href="#" data-href="' . action("\App\Http\Controllers\NotificationController@getTemplate", ["transaction_id" => $row->id, "template_for" => "new_quotation"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>' . __("lang_v1.new_quotation_notification") . '
                                        </a>
                                    </li>';

                            $html .= '<li>
                                        <a href="' . action("\App\Http\Controllers\SellPosController@showInvoiceUrl", [$row->id]) . '" class="view_invoice_url"><i class="fas fa-eye"></i>' . __("lang_v1.view_quote_url") . '</a>
                                    </li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if (! empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }

                    if ($row->sub_status == 'proforma') {
                        $invoice_no .= '<br><span class="label bg-gray">' . __('lang_v1.proforma_invoice') . '</span>';
                    }

                    if (! empty($row->is_export)) {
                        $invoice_no .= '</br><small class="label label-default no-print" title="' . __('lang_v1.export') . '">' . __('lang_v1.export') . '</small>';
                    }

                    return $invoice_no;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('total_items', '{{@format_quantity($total_items)}}')
                ->editColumn('total_quantity', '{{@format_quantity($total_quantity)}}')
                ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br>@endif {{$name}}')
                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('added_by', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->rawColumns(['action', 'invoice_no', 'transaction_date', 'conatct_name'])
                ->make(true);
        }
    }

    /**
     * Creates copy of the requested sale.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicateSell($id)
    {
        if (! auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $transaction = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->findorfail($id);
            $duplicate_transaction_data = [];
            foreach ($transaction->toArray() as $key => $value) {
                if (! in_array($key, ['id', 'created_at', 'updated_at'])) {
                    $duplicate_transaction_data[$key] = $value;
                }
            }
            $duplicate_transaction_data['status'] = 'draft';
            $duplicate_transaction_data['payment_status'] = null;
            $duplicate_transaction_data['transaction_date'] = \Carbon::now();
            $duplicate_transaction_data['created_by'] = $user_id;
            $duplicate_transaction_data['invoice_token'] = null;

            DB::beginTransaction();
            $duplicate_transaction_data['invoice_no'] = $this->transactionUtil->getInvoiceNumber($business_id, 'draft', $duplicate_transaction_data['location_id']);

            //Create duplicate transaction
            $duplicate_transaction = Transaction::create($duplicate_transaction_data);

            //Create duplicate transaction sell lines
            $duplicate_sell_lines_data = [];

            foreach ($transaction->sell_lines as $sell_line) {
                $new_sell_line = [];
                foreach ($sell_line->toArray() as $key => $value) {
                    if (! in_array($key, ['id', 'transaction_id', 'created_at', 'updated_at', 'lot_no_line_id'])) {
                        $new_sell_line[$key] = $value;
                    }
                }

                $duplicate_sell_lines_data[] = $new_sell_line;
            }

            $duplicate_transaction->sell_lines()->createMany($duplicate_sell_lines_data);

            DB::commit();

            $output = [
                'success' => 0,
                'msg' => trans('lang_v1.duplicate_sell_created_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        if (! empty($duplicate_transaction)) {
            if ($duplicate_transaction->is_direct_sale == 1) {
                return redirect()->action([\App\Http\Controllers\SellController::class, 'edit'], [$duplicate_transaction->id])->with(['status', $output]);
            } else {
                return redirect()->action([\App\Http\Controllers\SellPosController::class, 'edit'], [$duplicate_transaction->id])->with(['status', $output]);
            }
        } else {
            abort(404, 'Not Found.');
        }
    }

    /**
     * Shows modal to edit shipping details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editShipping($id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $transaction = Transaction::where('business_id', $business_id)
            ->with(['media', 'media.uploaded_by_user'])
            ->findorfail($id);


        $users = User::forDropdown($business_id, false, false, false);

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $activity_table = config('activitylog.table_name', 'activity_logs');
        $activities = Activity::forSubject($transaction)
            ->with(['causer', 'subject'])
            ->where($activity_table.'.description', 'shipping_edited')
            ->latest()
            ->get();

        return view('sell.partials.edit_shipping')
            ->with(compact('transaction', 'shipping_statuses', 'activities', 'users'));
    }

    /**
     * Update shipping.
     *
     * @param  Request  $request, int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateShipping(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                'shipping_details',
                'shipping_address',
                'shipping_status',
                'delivered_to',
                'delivery_person',
                'shipping_custom_field_1',
                'shipping_custom_field_2',
                'shipping_custom_field_3',
                'shipping_custom_field_4',
                'shipping_custom_field_5',
            ]);


            $business_id = $request->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                ->findOrFail($id);

            $transaction_before = $transaction->replicate();
            $old_shipping_status = $transaction->shipping_status;

            $transaction->update($input);

            $activity_property = ['update_note' => $request->input('shipping_note', '')];
            $this->transactionUtil->activityLog($transaction, 'shipping_edited', $transaction_before, $activity_property);

            // If shipping_status was changed to 'shipped', create tracking status and send notification
            if (isset($input['shipping_status']) && $input['shipping_status'] === 'shipped' && $old_shipping_status !== 'shipped') {
                // Automatically create tracking status when order is shipped
                try {
                    \App\Models\OrderTrackingStatus::updateOrCreate(
                        [
                            'transaction_id' => $transaction->id,
                            'status' => 'shipped',
                        ],
                        [
                            'status_date' => now(),
                            'updated_by' => auth()->user()->id ?? null,
                        ]
                    );
                    Log::info('Tracking status created: shipped (from updateShipping)', [
                        'transaction_id' => $transaction->id,
                        'previous_status' => $old_shipping_status
                    ]);
                } catch (\Exception $trackingError) {
                    Log::error('Failed to create shipped tracking status (from updateShipping)', [
                        'transaction_id' => $transaction->id,
                        'error' => $trackingError->getMessage()
                    ]);
                    // Don't fail the whole operation if tracking fails
                }

                // Send notification to customer when order is shipped
                try {
                    Log::info('Attempting to send order shipped notification (from updateShipping)', [
                        'transaction_id' => $transaction->id,
                        'contact_id' => $transaction->contact_id,
                        'business_id' => $business_id,
                        'transaction_type' => $transaction->type
                    ]);

                    $contact = Contact::find($transaction->contact_id);
                    if ($contact) {
                        Log::info('Contact found for order shipped notification (from updateShipping)', [
                            'transaction_id' => $transaction->id,
                            'contact_id' => $contact->id,
                            'contact_email' => $contact->email ?? 'no email',
                            'contact_mobile' => $contact->mobile ?? 'no mobile'
                        ]);

                        // Determine which transaction to use for notification
                        $notificationTransaction = $transaction;
                        
                        // For sales orders, prefer the related invoice (type='sell') if it exists
                        if ($transaction->type === 'sales_order') {
                            $relatedInvoice = Transaction::where('type', 'sell')
                                ->where('business_id', $transaction->business_id)
                                ->where('contact_id', $transaction->contact_id)
                                ->where(function($query) use ($transaction) {
                                    // Check if sales_order_ids JSON array contains this transaction ID
                                    $query->whereRaw('JSON_CONTAINS(sales_order_ids, ?)', [json_encode((string)$transaction->id)]);
                                })
                                ->first();
                            
                            if ($relatedInvoice) {
                                // Use the invoice transaction for notification as it has more complete shipping info
                                $notificationTransaction = $relatedInvoice;
                                Log::info('Using related invoice for notification (from updateShipping)', [
                                    'transaction_id' => $transaction->id,
                                    'invoice_id' => $relatedInvoice->id
                                ]);
                            }
                        }

                             $isB2C = false;

                            if($transaction->location_id){
                                $location = BusinessLocation::find($transaction->location_id);
                                if($location->is_b2c){
                                    $isB2C = true;
                                }
                            }
                            if($isB2C){
                                $custom_data = (object) [
                                    'contact_id' => $contact->id,
                                    'transaction' => $invoiceTransaction,
                                    'brand_id' => $contact->brand_id,
                                    'is_b2c' => true,
                                    'email' => $contact->email,
                                ];
                                SendNotificationJob::dispatch(
                                    false,
                                    $business_id,
                                    'order_shipped', 
                                    $contact,
                                    $custom_data,
                                    $invoiceTransaction 
                                );
                            }else{
                            SendNotificationJob::dispatch(
                                false, // is_custom = false
                                $business_id,
                                'order_shipped', // Using order_shipped notification type
                                null, // user
                                $contact,
                                $invoiceTransaction // Use invoice transaction for notification
                            );
                        }
                    }
                } catch (\Exception $notificationError) {
                    Log::error('Failed to queue order shipped notification (from updateShipping)', [
                        'transaction_id' => $transaction->id,
                        'contact_id' => $transaction->contact_id ?? null,
                        'error' => $notificationError->getMessage(),
                        'file' => $notificationError->getFile(),
                        'line' => $notificationError->getLine(),
                        'trace' => $notificationError->getTraceAsString()
                    ]);
                    // Don't fail the whole operation if notification fails
                }
            }

            $output = [
                'success' => 1,
                'msg' => trans('lang_v1.updated_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Display list of shipments.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipments()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, true);

        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $delevery_person = User::forDropdown($business_id, false, false, true);

        return view('sell.shipments')->with(compact('shipping_statuses'))
            ->with(compact('business_locations', 'customers', 'sales_representative', 'is_service_staff_enabled', 'service_staffs', 'delevery_person'));
    }

    public function viewMedia($model_id)
    {
        if (request()->ajax()) {
            $model_type = request()->input('model_type');
            $business_id = request()->session()->get('user.business_id');

            $query = Media::where('business_id', $business_id)
                ->where('model_id', $model_id)
                ->where('model_type', $model_type);

            $title = __('lang_v1.attachments');
            if (! empty(request()->input('model_media_type'))) {
                $query->where('model_media_type', request()->input('model_media_type'));
                $title = __('lang_v1.shipping_documents');
            }

            $medias = $query->get();

            return view('sell.view_media')->with(compact('medias', 'title'));
        }
    }

    public function resetMapping()
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        Artisan::call('pos:mapPurchaseSell');

        echo 'Mapping reset success';
        exit;
    }
    public function openSellNoteModal(Request $request, $id)
    {

        $business_id = request()->session()->get('user.business_id');

        $query = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['contact', 'delivery_person_user', 'sell_lines' => function ($q) {
                $q->whereNull('parent_sell_line_id');
            }, 'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.product.second_unit', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'sell_lines.variations.group_prices', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff', 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties', 'media']);

        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }

        $sell = $query->firstOrFail();

        $statuses = Transaction::sell_statuses();

        if ($sell->type == 'sales_order') {
            $sales_order_statuses = Transaction::sales_order_statuses(true);
            $statuses = array_merge($statuses, $sales_order_statuses);
        }
        return view('sale_pos.modals.sellNote_modal')->with(['sell' => $sell]);
    }

    public function updateSellNote(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        // Check permissions
        if (!auth()->user()->can('sell.update') && !auth()->user()->can('direct_sell.update') && !auth()->user()->can('so.update')) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized action.'
            ], 403);
        }

        $transaction = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->whereIn('type', ['sell', 'sales_order'])
            ->firstOrFail();

        $transaction->additional_notes = $request->input('additional_notes');
        $transaction->save();

        return response()->json([
            'success' => true,
            'msg' => __('lang_v1.success')
        ]);
    }

    public function openEditShippingAddressModal($id){
        $sell = Transaction::find($id);
        return view('sale_pos.modals.edit_shippingaddress', compact('sell'));
    }
    public function updateShippingAddressTransaction(Request $request){
        $sell = Transaction::find($request->id);
        $sell->shipping_company = $request->shipping_company;
        $sell->shipping_first_name = $request->shipping_first_name;
        $sell->shipping_last_name = $request->shipping_last_name;
        $sell->shipping_address1 = $request->shipping_address1;
        $sell->shipping_address2 = $request->shipping_address2;
        $sell->shipping_city = $request->shipping_city;
        $sell->shipping_state = $request->shipping_state;
        $sell->shipping_zip = $request->shipping_zip;
        $sell->shipping_country = $request->shipping_country;
        $sell->save();
        return response()->json(['status' => true, 'message' => 'Shipping address updated successfully.']);
    }

    /**
     * Summary of updateSellsLine
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updateSellsLine(Request $request)
    {
        $data = $request->input('data');
        $type = $request->input('type');
        if ($type == 'purchase') {
            $checkOnce = false;
            foreach ($data as $line) {
                $purchase_line_id = $line['purchase_line_id'];
                $unitPrice = (float) $line['unit_price'];
                $newQty = (int) $line['quantity'];
                $discount = (float) $line['discount'];
                $discount_type = $line['discount_type'];
                $tax = 0; // no tax in PO (float) preg_replace('/\^?\$?\s*/', '', $line['tax']);


                // update purchase line
                $purchaseLine = PurchaseLine::find($purchase_line_id);

                if (!$checkOnce) {
                    $checkOnce = true;
                    $orderFulfillmentController = app(OrderfulfillmentController::class);
                    $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $purchaseLine->transaction_id, true);
                    if ($lockModal && $lockModal['status'] == false) {
                        return response()->json(['status' => false, 'message' => $lockModal['message']]);
                    }
                }

                if ($purchaseLine->po_quantity_purchased > $newQty) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Noticed, You tried to reduce below the consumed quantity.'
                    ]);
                }

                // Store old values for comparison
                $oldQty = $purchaseLine->quantity;
                $oldPurchasePrice = $purchaseLine->purchase_price;
                $oldPurchasePriceIncTax = $purchaseLine->purchase_price_inc_tax;

                // Update purchase line values
                $purchaseLine->quantity = $newQty;
                $purchaseLine->pp_without_discount = $unitPrice;
                $purchaseLine->discount_percent = $discount; // now considered as value only 
                $purchaseLine->row_discount_type = $discount_type;

                // Calculate new purchase price after discount
                if ($discount_type == 'percentage') {
                    $purchaseLine->purchase_price = round($unitPrice - ($unitPrice * $discount / 100), 2);
                } else {
                    $purchaseLine->purchase_price = round($unitPrice - $discount, 2);
                }

                // Calculate new purchase price with tax
                $purchaseLine->purchase_price_inc_tax = round($purchaseLine->purchase_price + $tax, 2);
                $purchaseLine->item_tax = $tax;

                // Calculate the difference in totals
                $oldLineTotal = $oldPurchasePrice * $oldQty;
                $newLineTotal = $purchaseLine->purchase_price * $newQty;
                $totalDifference = $newLineTotal - $oldLineTotal;

                $oldLineTotalWithTax = $oldPurchasePriceIncTax * $oldQty;
                $newLineTotalWithTax = $purchaseLine->purchase_price_inc_tax * $newQty;
                $totalDifferenceWithTax = $newLineTotalWithTax - $oldLineTotalWithTax;

                // Save purchase line
                $purchaseLine->save();

                // Update transaction totals
                $transaction = $purchaseLine->transaction;
                $transaction->total_before_tax += $totalDifference;
                $transaction->final_total += $totalDifferenceWithTax;
                $transaction->save();
            }
            return response()->json([
                'status' => true,
                'message' => 'Purchase lines updated successfully.',
                'order' => $transaction


            ]);
        } else if ($type == 'delete-sell-line') {
            return response()->json(['status' => true, 'message' => 'Hello World']);
        } else {
            $totalTax = 0;
            $checkOnce = false;
            try {
                DB::beginTransaction();
                foreach ($data as $line) {
                    $transaction_id = $line['transaction_id'];
                    $product_id = $line['product_id'] ?? '2532';
                    $variation_id = $line['variation_id'];
                    $sell_line_id = $line['sell_line_id'];
                    $unitPrice = (float) $line['unit_price'];
                    $newQty = (int) $line['quantity'];
                    $discount = (float) $line['discount'];
                    $tax = (float) preg_replace('/\^?\$?\s*/', '', $line['tax']);
                    $isNewSellLine = $sell_line_id == 0 ? true : false;
                    if ($unitPrice < 0 || $newQty < 0 || $discount < 0 || $tax < 0) {
                        return response()->json(['status' => false, 'message' => 'All values should be positive.']);
                    }
                    if ($isNewSellLine) {
                        $saleLine = new TransactionSellLine();
                        $saleLine->transaction_id = $transaction_id;
                        $saleLine->product_id = $product_id;
                        $saleLine->variation_id = $variation_id;
                        $saleLine->unit_price_before_discount = $unitPrice;
                        $saleLine->unit_price = $unitPrice;
                        $saleLine->unit_price_inc_tax = $unitPrice + $tax;
                        $saleLine->item_tax = $tax;
                        $saleLine->quantity = $newQty;
                        $saleLine->ordered_quantity = $newQty;
                        $saleLine->line_discount_amount = $discount;

                        $transaction = Transaction::find($transaction_id);
                        $lineTotal = ($unitPrice - $discount) * $newQty;
                        $lineTaxTotal = $tax * $newQty;
                        $lineTotalWithTax = $lineTotal + $lineTaxTotal;

                        $transaction->total_before_tax += $lineTotal;
                        $transaction->final_total += $lineTotalWithTax;
                        $transaction->tax_amount += $lineTaxTotal;

                        // order fulfillment case
                        if ($transaction->type == 'sales_order' && $transaction->status == 'ordered') {
                            if ($transaction->picking_status == 'PICKING') { // processing case
                                if ($transaction->isPicked == false && $transaction->isVerified == false) {
                                    // no need to fill picked qty 
                                } else if ($transaction->isPicked == true && $transaction->isVerified == false) {
                                    $saleLine->picked_quantity = $newQty;
                                } else if ($transaction->isPicked == true && $transaction->isVerified == true) {
                                    $saleLine->picked_quantity = $newQty;
                                    $saleLine->verified_qty = $newQty;
                                }
                            } else if ($transaction->picking_status == 'PICKED') { // picked case
                                $saleLine->picked_quantity = $newQty;
                                $saleLine->verified_qty = $newQty;
                            } else if ($transaction->picking_status == null) { // pending case
                                // no need to fill picked qty 
                            }
                        } else {
                            return response()->json(['status' => false, 'message' => 'Order not in edit state.']);
                        }
                        $saleLine->save();
                        $transaction->save();

                        // fix the qty of PR on Invoice change
                        if ($transaction->type == 'sell') {
                            // Handle purchase line mapping for new sell line
                            $purchaseLines = PurchaseLine::where('product_id', $product_id)
                            ->where('variation_id', $variation_id)
                            ->whereHas('transaction', function ($query) {
                                $query->where('type', 'purchase')->where('status', 'received');
                            })
                            ->orderBy('transaction_id')
                            ->get();
            
                            $existingMappings = DB::table('transaction_sell_lines_purchase_lines')
                                ->where('sell_line_id', $sell_line_id)
                                ->pluck('quantity', 'purchase_line_id');
                
                            foreach ($existingMappings as $purchaseLineId => $qtyUsed) {
                                $purchaseLine = PurchaseLine::find($purchaseLineId);
                                if ($purchaseLine) {
                                    $purchaseLine->quantity_sold -= $qtyUsed;
                                    $purchaseLine->save();
                                }
                            }
                            DB::table('transaction_sell_lines_purchase_lines')->where('sell_line_id', $sell_line_id)->delete();

                            $qtyRemaining = $newQty;
                            foreach ($purchaseLines as $purchaseLine) {
                                $available = $purchaseLine->quantity - $purchaseLine->quantity_sold;
                                $useQty = min($qtyRemaining, $available);

                                if ($useQty > 0) {
                                    $purchaseLine->quantity_sold += $useQty;
                                    $purchaseLine->save();

                                    DB::table('transaction_sell_lines_purchase_lines')->insert([
                                        'sell_line_id' => $sell_line_id,
                                        'purchase_line_id' => $purchaseLine->id,
                                        'quantity' => $useQty,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);

                                    $qtyRemaining -= $useQty;
                                }

                                if ($qtyRemaining <= 0) break;
                            }
                        } else if ($transaction->type == 'sales_order') {
                            // sell order decrease qty from instock qty 
                            $product = Product::find($product_id);
                            if ($product && $product->enable_stock == 1) {
                                $stock = VariationLocationDetails::where('product_id', $product_id)
                                    ->where('variation_id', $variation_id)
                                    ->first();
                                if ($stock) {
                                    try {
                                        $stock->in_stock_qty -= $newQty;
                                    } catch (\Throwable $th) {
                                        $stock->in_stock_qty = 0;
                                    }
                                    $stock->save();
                                }
                            }
                        }
                    } else {
                        $saleLine = TransactionSellLine::with('transaction')->find($sell_line_id);
                        if (!$saleLine) {
                            return response()->json(['status' => false, 'message' => "Sale line not found for ID $sell_line_id."]);
                        }
                        if ($saleLine->transaction->payment_status != 'due') {
                            return response()->json(['status' => false, 'message' => "Sale cannot be edited due to payments."]);
                        }
                        if (!$checkOnce) {
                            $checkOnce = true;
                            $orderFulfillmentController = app(OrderfulfillmentController::class);
                            $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $saleLine->transaction_id, true);
                            if ($lockModal && $lockModal['status'] == false) {
                                return response()->json(['status' => false, 'message' => $lockModal['message']]);
                            }
                        }
                        $order = $saleLine->transaction;
                        $location_id = $order->location_id;
                        $product_id = $saleLine->product_id;
                        $variation_id = $saleLine->variation_id;
                        $oldQty = (int) $saleLine->quantity;
                        $oldPrice = (float) $saleLine->unit_price;
                        $oldDiscount = (float) $saleLine->discount;
                        $oldTax = (float) $saleLine->item_tax;

                        // fix the qty of PR on Invoice change
                        if ($order->type == 'sell') {
                            // NEW LOGIC TO DISTRIBUTE QUANTITY ACROSS MULTIPLE PURCHASE LINES
                            $purchaseLines = PurchaseLine::where('product_id', $product_id)
                                ->where('variation_id', $variation_id)
                                ->whereHas('transaction', function ($query) {
                                    $query->where('type', 'purchase')->where('status', 'received');
                                })
                                ->orderBy('transaction_id')
                                ->get();
                
                            $existingMappings = DB::table('transaction_sell_lines_purchase_lines')
                                ->where('sell_line_id', $sell_line_id)
                                ->pluck('quantity', 'purchase_line_id');
                
                            foreach ($existingMappings as $purchaseLineId => $qtyUsed) {
                                $purchaseLine = PurchaseLine::find($purchaseLineId);
                                if ($purchaseLine) {
                                    $purchaseLine->quantity_sold -= $qtyUsed;
                                    $purchaseLine->save();
                                }
                            }
                
                            DB::table('transaction_sell_lines_purchase_lines')->where('sell_line_id', $sell_line_id)->delete();
                
                            $qtyRemaining = $newQty;
                            foreach ($purchaseLines as $purchaseLine) {
                                $available = $purchaseLine->quantity - $purchaseLine->quantity_sold;
                                $useQty = min($qtyRemaining, $available);
                
                                if ($useQty > 0) {
                                    $purchaseLine->quantity_sold += $useQty;
                                    $purchaseLine->save();
                
                                    DB::table('transaction_sell_lines_purchase_lines')->insert([
                                        'sell_line_id' => $sell_line_id,
                                        'purchase_line_id' => $purchaseLine->id,
                                        'quantity' => $useQty,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);
                
                                    $qtyRemaining -= $useQty;
                                }
                
                                if ($qtyRemaining <= 0) break;
                            }
                        }

                        $product = Product::find($product_id);
                        $stock = VariationLocationDetails::where('product_id', $product_id)
                            ->where('variation_id', $variation_id)
                            ->where('location_id', $location_id)
                            ->first();
                        if (!$stock && $product && $product->enable_stock == 1) {
                            return response()->json(['status' => false, 'message' => 'Stock details not found for the product.']);
                        }
                        // invoice case 
                        $qtyChange = $newQty - $oldQty;
                        if ($order->type == 'sell' && $product && $product->enable_stock == 1) {
                            if ($qtyChange < 0) { // if decrease, increase stock 
                                $upqty = abs($qtyChange);

                                $stock->qty_available +=  $upqty;
                                if ($stock->qty_available <= 0) {
                                    $remaining = TransactionSellLine::with(['transaction'])
                                        ->whereHas('transaction', function ($query) use ($location_id) {
                                            $query->where('type', 'sales_order')
                                                ->where('status', 'ordered')
                                                ->where(function ($q) {
                                                    $q->whereNotIn('picking_status', ['INVOICED'])
                                                        ->orWhereNull('picking_status');
                                                })
                                                ->where('location_id', $location_id);
                                        })
                                        ->where('variation_id', $variation_id)
                                        ->where('product_id', $product_id)
                                        ->sum('ordered_quantity');
                                    $add_in_stock_qty = $upqty - $remaining;
                                    if ($remaining == 0) {
                                        try {
                                            $stock->in_stock_qty = $stock->qty_available;
                                        } catch (\Throwable $th) {
                                            $stock->in_stock_qty = 0;
                                        }
                                    } else if ($add_in_stock_qty <= 0) {
                                        $stock->in_stock_qty = 0;
                                    } else {
                                        $stock->in_stock_qty = $add_in_stock_qty;
                                    }
                                } else {
                                    $remaining = TransactionSellLine::with(['transaction'])
                                        ->whereHas('transaction', function ($query) use ($location_id) {
                                            $query->where('type', 'sales_order')
                                                ->where('status', 'ordered')
                                                ->where(function ($q) {
                                                    $q->whereNotIn('picking_status', ['INVOICED'])
                                                        ->orWhereNull('picking_status');
                                                })
                                                ->where('location_id', $location_id);
                                        })
                                        ->where('variation_id', $variation_id)
                                        ->where('product_id', $product_id)
                                        ->sum('ordered_quantity');
                                    $stock->in_stock_qty += $stock->qty_available - $remaining;
                                }
                            } else { // if increase, decrease stock 
                                $stock->qty_available -= $qtyChange;
                                try {
                                    $stock->in_stock_qty -= $qtyChange;
                                } catch (\Throwable $th) {
                                    $stock->in_stock_qty = 0;
                                }
                            }
                        } else if ($order->type == 'sales_order') {
                            // order fulfillment case
                            if ($order->status == 'ordered') {
                                if ($order->picking_status == 'PICKING') { // processing case
                                    if ($order->isPicked == false && $order->isVerified == false) {
                                        // no need to fill picked qty 
                                    } else if ($order->isPicked == true && $order->isVerified == false) {
                                        $saleLine->picked_quantity = $newQty;
                                    } else if ($order->isPicked == true && $order->isVerified == true) {
                                        $saleLine->picked_quantity = $newQty;
                                        $saleLine->verified_qty = $newQty;
                                    }
                                } else if ($order->picking_status == 'PICKED') { // picked case
                                    $saleLine->picked_quantity = $newQty;
                                    $saleLine->verified_qty = $newQty;
                                } else if ($order->picking_status == null) { // pending case
                                    // no need to fill picked qty 
                                }
                            } else {
                                return response()->json(['status' => false, 'message' => 'Order not in edit state.']);
                            }

                            // stock case - only manage stock if enable_stock is true
                            if ($product && $product->enable_stock == 1 && $stock) {
                                if($qtyChange < 0){ // if decrease, increase stock 
                                    try {
                                        $stock->in_stock_qty += $qtyChange;
                                    } catch (\Throwable $th) {
                                        $stock->in_stock_qty = 0;
                                    }
                                } else { // if increase, decrease stock 
                                    try {
                                        $stock->in_stock_qty -= $qtyChange;
                                    } catch (\Throwable $th) {
                                        $stock->in_stock_qty = 0;
                                    }
                                }
                                $stock->save();
                            }
                        } else if ($product && $product->enable_stock == 1 && $stock) {
                            $stock->save();
                        }
                        $oldAmount = ($oldPrice - $oldDiscount) * $oldQty;
                        $oldTaxAmount = $oldTax * $oldQty;
                        $oldAmountWithTax = $oldAmount + $oldTaxAmount;
                        $newAmount = ($unitPrice - $discount) * $newQty;
                        $newTaxAmount = $tax * $newQty;
                        $newAmountWithTax = $newAmount + $newTaxAmount;
                        $order->total_before_tax += ($newAmount - $oldAmount);
                        $order->final_total += ($newAmountWithTax - $oldAmountWithTax);
                        $order->tax_amount += ($newTaxAmount - $oldTaxAmount);
                        $order->save();
                        $saleLine->unit_price_before_discount = $unitPrice;
                        $saleLine->unit_price = $unitPrice;
                        $saleLine->unit_price_inc_tax = $unitPrice + $tax;
                        $saleLine->item_tax = $tax;
                        $saleLine->quantity = $newQty;
                        $saleLine->save();
                        $totalTax += $newTaxAmount;
                    }
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Something went wrong. ' . $th->getMessage() . 'on line ' . $th->getLine()]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Sale lines updated successfully.',
                'total_tax' => $totalTax,
                'order' => $order
            ]);
        }
    }
    public function shipmentDetails($id)
    {

        $sale = Transaction::with([
            'sell_lines',
            'contact',
            'sell_lines.product' => function ($query) {
                $query->select('id', 'name', 'slug', 'image');
            },
            'sell_lines.variations' => function ($query) {
                $query->select('id', 'product_id', 'name', 'sub_sku', 'var_barcode_no');
            }
        ])->find($id);
        $shipment = $sale->shipment;
        $wareHouse = ShipStation::find($shipment['shipment_details']['warehouse_id']);
        $user = $sale->contact;
        return view('sale_pos.modals.shipment_details_modal')->with(compact('shipment', 'sale', 'wareHouse', 'user'));
    }

    public function showShipmentEmail()
    {
        return view('sale_pos.modals.send_email');
    }

    public function getModalEntryRow(Request $request)
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

                return [
                    'product' => $product,
                    'variations' => $variations,
                    'row_count' => $row_count,
                    'variation_id' => $variation_id,
                    'taxes' => $taxes,
                    'currency_details' => $currency_details,
                    'sub_units' => $sub_units,
                    'is_purchase_order' => $is_purchase_order,
                    'last_purchase_line' => $last_purchase_line,
                ];
            }
        }
    }
    private function getLastPurchaseLine($variation_id, $location_id, $supplier_id = null)
    {
        $query = PurchaseLine::join(
            'transactions as t',
            'purchase_lines.transaction_id',
            '=',
            't.id'
        )
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
     public function downloadLabelPdf($id)
    {

         $sale = Transaction::with([
            'sell_lines',
            'contact',
            'sell_lines.product' => function ($query) {
                $query->select('id', 'name', 'slug', 'image');
            },
            'sell_lines.variations' => function ($query) {
                $query->select('id', 'product_id', 'name', 'sub_sku', 'var_barcode_no');
            }
        ])->find($id);
        $shipment = $sale->shipment;
        $wareHouse = ShipStation::find($shipment['shipment_details']['warehouse_id']);
        $user = $sale->contact;
        $body = view('sale_pos.receipts.shipment_label')
            ->with(compact('shipment', 'sale', 'wareHouse', 'user'))
            ->render();

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => public_path('uploads/temp'),
            'mode' => 'utf-8',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'autoVietnamese' => true,
            'autoArabic' => true,
            'margin_top' => 8,
            'margin_bottom' => 8,
            'format' => [101.6, 152.4], // 4x6 inches in mm
        ]);


        $mpdf->useSubstitutions = true;
        $mpdf->SetTitle('Shipment-' . $sale->id . '.pdf');
        $mpdf->WriteHTML($body);
        $mpdf->Output('PO-' . $sale->id . '.pdf', 'I');
    }

    /**
     * Cancel a sales order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelSalesOrder($id)
    {
        // Always return a JSON payload with HTTP 200 so the existing
        // JavaScript callback (`result.success`) can handle both
        // success and failure paths without hitting the generic
        // AJAX error handler.
        \Log::info('cancelSalesOrder called', [
            'order_id' => $id,
            'url' => request()->fullUrl(),
            'is_api' => request()->is('api/*'),
        ]);

        try {
            // Handle both web and API authentication
            $user = null;
            $is_api_request = request()->is('api/*');

            if ($is_api_request) {
                // API call - use api guard
                $user = Auth::guard('api')->user();
            } else {
                // Web call - use default guard
                $user = auth()->user();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Authentication required.'
                ]);
            }

            // For B2B customers, we don't need admin check, but keep the check
            // here for web users so we can add permission logic later if needed.
            if (!$is_api_request) {
                try {
                    $this->businessUtil->is_admin($user);
                } catch (\Throwable $e) {
                    \Log::error('BusinessUtil is_admin error: ' . $e->getMessage());
                }
            }

            // Get business_id from session (web) or user (API)
            $business_id = null;
            if ($is_api_request) {
                // API call - get business_id from contact / API user
                $business_id = $user->business_id ?? null;
            } else {
                // Web call - get from session
                $business_id = request()->session()->get('user.business_id');
            }

            if (empty($business_id)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Unable to determine business for this request.'
                ]);
            }

            DB::beginTransaction();

            // Eager-load both payment_lines and sell_lines to avoid lazy-load surprises
            $transaction = Transaction::with(['payment_lines', 'sell_lines'])
                ->where('type', 'sales_order')
                ->where('business_id', $business_id)
                ->find($id);

            if (!$transaction) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Transaction not found.'
                ]);
            }

            // Check if already cancelled
            if ($transaction->status === 'cancelled') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Order is already cancelled.'
                ]);
            }

            // Restore stock and handle sell lines (guard against missing relation)
            foreach ($transaction->sell_lines ?? [] as $sellLine) {
                $variation = Variation::with('variation_location_details', 'product')
                    ->where('id', $sellLine->variation_id)
                    ->first();

                if ($variation) {
                    $product = $variation->product;
                    $enable_stock = $product ? ($product->enable_stock ?? 1) : 1;
                    
                    // Only manage stock if enable_stock == 1
                    if ($enable_stock == 1) {
                        $location = $variation->variation_location_details->first();
                        if ($location) {
                            // Restore BOTH picked quantity AND ordered quantity back to stock
                            // This ensures stock returns to exactly what it was before the order was placed
                            $location->qty_available += ($sellLine->picked_quantity ?? 0) + ($sellLine->ordered_quantity ?? 0);
                            
                            // Calculate remaining ordered quantities from other sales orders
                            $remaining_ordered = DB::table('transaction_sell_lines as tsl')
                                ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
                                ->where('t.type', 'sales_order')
                                ->where('t.status', '!=', 'cancelled')
                                ->where('tsl.variation_id', $sellLine->variation_id)
                                ->where('t.id', '!=', $transaction->id)
                                ->sum('tsl.ordered_quantity');
                            
                            // Calculate virtual stock correctly
                            if ($location->qty_available < 0) {
                                // When hand stock is negative, virtual stock should be 0
                                $location->in_stock_qty = 0;
                            } else {
                                // When hand stock is positive, virtual stock is available minus remaining orders
                                $location->in_stock_qty = max(0, $location->qty_available - $remaining_ordered);
                            }
                            
                            $location->save();
                        }
                    }
                }
                // Reset both picked and ordered quantities to 0
                $sellLine->picked_quantity = 0;
                $sellLine->ordered_quantity = 0;
                $sellLine->save();
            }

            // Restore customer balance from payments
            $amount = 0;
            foreach ($transaction->payment_lines ?? [] as $paymentLine) {
                $amount += $paymentLine->amount;
            }

            if ($amount != 0 && !empty($transaction->contact_id)) {
                $contact = Contact::find($transaction->contact_id);
                if ($contact) {
                    $contact->balance = ($contact->balance ?? 0) + $amount;
                    $contact->save();
                }
            }

            // Update transaction status
            $transaction->status = 'cancelled';
            $transaction->picking_status = null;
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Order cancelled and stock restored.'
            ]);

        } catch (\Throwable $e) {
            // Make sure any open transaction is rolled back
            try {
                DB::rollBack();
            } catch (\Throwable $rollbackException) {
                \Log::error('Rollback failed in cancelSalesOrder: ' . $rollbackException->getMessage());
            }

            \Log::error('Cancel Sales Order Error', [
                'order_id' => $id,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'msg' => 'An unexpected error occurred while cancelling the order. Please try again or contact support.'
            ]);
        }
    }

    /**
     * Get order count for header notification
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrderCount()
    {
        if (!request()->ajax()) {
            abort(404);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $permitted_locations = auth()->user()->permitted_locations();
            
            \Log::info('getOrderCount called', [
                'business_id' => $business_id,
                'permitted_locations' => $permitted_locations
            ]);
            
            // Base query for orders (all dates for testing) that are not cancelled or void
            $baseQuery = \App\Transaction::where('business_id', $business_id)
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'void');
                // ->whereDate('transaction_date', today()); // Commented out for testing
            
            // Apply location permissions
            if ($permitted_locations != 'all') {
                $baseQuery->whereIn('location_id', $permitted_locations);
            }
            
            // Get counts for different payment statuses
            $dueCount = (clone $baseQuery)
                ->where('payment_status', 'due')
                ->count();
            
            $partialCount = (clone $baseQuery)
                ->where('payment_status', 'partial')
                ->count();
            
            $paidCount = (clone $baseQuery)
                ->where('payment_status', 'paid')
                ->count();
            
            // Get sales orders count
            $salesOrderCount = (clone $baseQuery)
                ->where('type', 'sales_order')
                ->count();
            
            // Calculate total (excluding sales orders from payment status totals to avoid double counting)
            $totalCount = $dueCount + $partialCount + $paidCount + $salesOrderCount;
            
            \Log::info('Order counts calculated', [
                'due' => $dueCount,
                'partial' => $partialCount,
                'paid' => $paidCount,
                'sales_orders' => $salesOrderCount,
                'total' => $totalCount
            ]);
            
            return response()->json([
                'success' => true,
                'counts' => [
                    'due' => $dueCount,
                    'partial' => $partialCount,
                    'paid' => $paidCount,
                    'sales_orders' => $salesOrderCount,
                    'total' => $totalCount
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getOrderCount', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'msg' => 'Error fetching order count: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get daily order reminder data
     *
     * @return \Illuminate\Http\Response
     */
    public function getDailyOrderReminder()
    {
        if (!request()->ajax()) {
            abort(404);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $permitted_locations = auth()->user()->permitted_locations();
            $user_last_login = auth()->user()->last_login_at ?? now()->subDays(1);
            
            \Log::info('getDailyOrderReminder called', [
                'business_id' => $business_id,
                'permitted_locations' => $permitted_locations,
                'last_login' => $user_last_login
            ]);
            
            // Base query for orders
            $baseQuery = \App\Transaction::where('business_id', $business_id)
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'void');
            
            // Apply location permissions
            if ($permitted_locations != 'all') {
                $baseQuery->whereIn('location_id', $permitted_locations);
            }
            
            // Get remaining orders (due, partial, or sales orders not completed)
            $remainingOrders = (clone $baseQuery)
                ->where(function($q) {
                    $q->whereIn('payment_status', ['due', 'partial'])
                      ->orWhere(function($subQ) {
                          $subQ->where('type', 'sales_order')
                                ->whereNotIn('picking_status', ['completed', 'delivered']);
                      });
                })
                ->with(['customer', 'business_location'])
                ->orderBy('transaction_date', 'asc')
                ->limit(10)
                ->get();
            
            // Get new orders since last login
            $newOrders = (clone $baseQuery)
                ->where('created_at', '>', $user_last_login)
                ->with(['customer', 'business_location'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Format remaining orders for display
            $remainingOrdersData = $remainingOrders->map(function($order) {
                return [
                    'id' => $order->id,
                    'invoice_no' => $order->invoice_no,
                    'customer' => $order->customer ? $order->customer->name : 'Walk-in Customer',
                    'amount' => number_format($order->final_total, 2),
                    'status' => $order->payment_status,
                    'type' => $order->type,
                    'location' => $order->business_location ? $order->business_location->name : 'Unknown',
                    'date' => \Carbon\Carbon::parse($order->transaction_date)->format('M j, Y')
                ];
            });
            
            // Format new orders for display
            $newOrdersData = $newOrders->map(function($order) {
                return [
                    'id' => $order->id,
                    'invoice_no' => $order->invoice_no,
                    'customer' => $order->customer ? $order->customer->name : 'Walk-in Customer',
                    'amount' => number_format($order->final_total, 2),
                    'status' => $order->payment_status,
                    'type' => $order->type,
                    'location' => $order->business_location ? $order->business_location->name : 'Unknown',
                    'created_at' => \Carbon\Carbon::parse($order->created_at)->format('H:i A')
                ];
            });
            
            $totalReminderCount = $remainingOrdersData->count() + $newOrdersData->count();
            
            \Log::info('Daily reminder data calculated', [
                'remaining_count' => $remainingOrdersData->count(),
                'new_count' => $newOrdersData->count(),
                'total' => $totalReminderCount
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'remaining_orders' => $remainingOrdersData,
                    'new_orders' => $newOrdersData,
                    'remaining_count' => $remainingOrdersData->count(),
                    'new_count' => $newOrdersData->count(),
                    'total_count' => $totalReminderCount,
                    'last_login' => $user_last_login->format('M j, Y H:i')
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getDailyOrderReminder', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'msg' => 'Error fetching daily reminder: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get order statistics for dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrderStats()
    {
        if (!request()->ajax()) {
            abort(404);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $permitted_locations = auth()->user()->permitted_locations();
            
            \Log::info('getOrderStats called', [
                'business_id' => $business_id,
                'permitted_locations' => $permitted_locations
            ]);
            
            // Base query for sales orders - filter by today's date
            $today = now()->startOfDay();
            $baseQuery = \App\Transaction::where('business_id', $business_id)
                ->where('type', 'sales_order')
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'void')
                ->whereDate('created_at', $today);
            
            // Apply location permissions
            if ($permitted_locations != 'all') {
                $baseQuery->whereIn('location_id', $permitted_locations);
            }
            
            // Count orders by status
            // Processing: orders that are pending/picking status is null or processing
            $processingCount = (clone $baseQuery)
                ->where(function($q) {
                    $q->whereNull('picking_status')
                      ->orWhere('picking_status', 'PROCESSING')
                      ->orWhere('picking_status', 'PENDING');
                })
                ->count();
            
            // Picking: orders that are in picking status (PICKED, PICKING, etc.)
            $pickingCount = (clone $baseQuery)
                ->where(function($q) {
                    $q->where('picking_status', 'PICKED')
                      ->orWhere('picking_status', 'PICKING')
                      ->orWhere('picking_status', 'PACKED');
                })
                ->count();
            
            // Completed: orders that are completed/delivered/invoiced
            $completedCount = (clone $baseQuery)
                ->where(function($q) {
                    $q->where('picking_status', 'completed')
                      ->orWhere('picking_status', 'delivered')
                      ->orWhere('picking_status', 'INVOICED')
                      ->orWhere('status', 'completed');
                })
                ->count();
            
            // Total Sales: all sell transactions for today (not just orders)
            $salesQuery = \App\Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'void')
                ->whereDate('created_at', $today);
            
            if ($permitted_locations != 'all') {
                $salesQuery->whereIn('location_id', $permitted_locations);
            }
            
            $totalSalesCount = $salesQuery->count();
            
            \Log::info('Order stats calculated', [
                'processing' => $processingCount,
                'picking' => $pickingCount,
                'completed' => $completedCount,
                'total_sales' => $totalSalesCount
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'processing_count' => $processingCount,
                    'picking_count' => $pickingCount,
                    'completed_count' => $completedCount,
                    'total_sales_count' => $totalSalesCount
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getOrderStats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'msg' => 'Error fetching order stats: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate gift cards for gift card products in an invoice
     */
    private function generateGiftCardsForInvoice($invoiceTransactionId, $order, $business_id)
    {
        // Get the invoice transaction with sell lines
        $invoiceTransaction = Transaction::with(['sell_lines.product', 'contact'])
            ->where('id', $invoiceTransactionId)
            ->first();
            
        if (!$invoiceTransaction) {
            Log::error('Invoice transaction not found for gift card generation', [
                'invoice_transaction_id' => $invoiceTransactionId
            ]);
            return;
        }
        
        $contact = $invoiceTransaction->contact;
        if (!$contact) {
            Log::error('Contact not found for gift card generation', [
                'invoice_transaction_id' => $invoiceTransactionId
            ]);
            return;
        }
        
        $giftCardsGenerated = [];
        
        // Process each sell line
        foreach ($invoiceTransaction->sell_lines as $sellLine) {
            $product = $sellLine->product;
            
            // Check if this is a gift card product
            if ($product && $product->is_gift_card) {
                $quantity = $sellLine->quantity;
                $unitPrice = $sellLine->unit_price;
                
                // Generate gift card codes for each quantity
                for ($i = 0; $i < $quantity; $i++) {
                    try {
                        // Generate unique gift card code
                        $code = \App\GiftCard::generateUniqueCode();
                        
                        // Calculate expiry date based on product settings
                        $expiresAt = null;
                        if ($product->gift_card_expires_at) {
                            $expiresAt = $product->gift_card_expires_at;
                        } elseif (isset($product->gift_card_expiry_days)) {
                            $expiresAt = now()->addDays($product->gift_card_expiry_days);
                        }
                        
                        // Create the gift card
                        $giftCard = \App\GiftCard::create([
                            'business_id' => $business_id,
                            'code' => $code,
                            'initial_amount' => $unitPrice,
                            'balance' => $unitPrice,
                            'currency' => 'USD',
                            'purchaser_contact_id' => $contact->id,
                            'created_by_user_id' => auth()->user()->id ?? 1,
                            'type' => 'egift', // Electronic gift card
                            'recipient_name' => $contact->name,
                            'recipient_email' => $contact->email,
                            'message' => "Gift card from order #{$order->invoice_no}",
                            'status' => 'active',
                            'purchased_at' => now(),
                            'expires_at' => $expiresAt,
                            'stock_quantity' => 1,
                        ]);
                        
                        $giftCardsGenerated[] = [
                            'code' => $code,
                            'amount' => $unitPrice,
                            'product_name' => $product->name,
                            'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : null
                        ];
                        
                        Log::info('Gift card generated successfully', [
                            'gift_card_id' => $giftCard->id,
                            'code' => $code,
                            'amount' => $unitPrice,
                            'contact_id' => $contact->id,
                            'invoice_id' => $invoiceTransactionId
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('Failed to generate individual gift card', [
                            'product_id' => $product->id,
                            'quantity_index' => $i,
                            'error' => $e->getMessage(),
                            'invoice_id' => $invoiceTransactionId
                        ]);
                        // Continue with next gift card even if one fails
                    }
                }
            }
        }
        
        // Send email with gift card codes if any were generated
        if (!empty($giftCardsGenerated) && !empty($contact->email)) {
            try {
                // Prepare email data
                $emailData = [
                    'customer_name' => $contact->name,
                    'order_number' => $order->invoice_no,
                    'invoice_number' => $invoiceTransaction->invoice_no,
                    'gift_cards' => $giftCardsGenerated,
                    'business_name' => $order->business->name ?? 'Your Business',
                    'support_email' => 'support@yourbusiness.com'
                ];
                
                // Send email using Laravel's Mail system
                \Mail::send('emails.gift_cards_generated', $emailData, function ($message) use ($contact, $order) {
                    $message->to($contact->email, $contact->name)
                        ->subject('Your Gift Cards from Order #' . $order->invoice_no)
                        ->from('noreply@yourbusiness.com', config('app.name', 'Your Business'));
                });
                
                Log::info('Gift card email sent successfully', [
                    'contact_id' => $contact->id,
                    'email' => $contact->email,
                    'gift_cards_count' => count($giftCardsGenerated),
                    'invoice_id' => $invoiceTransactionId
                ]);
                
            } catch (\Exception $emailError) {
                Log::error('Failed to send gift card email', [
                    'contact_id' => $contact->id,
                    'email' => $contact->email,
                    'error' => $emailError->getMessage(),
                    'invoice_id' => $invoiceTransactionId
                ]);
            }
        } elseif (!empty($giftCardsGenerated)) {
            Log::warning('Gift cards generated but no email address found', [
                'contact_id' => $contact->id,
                'gift_cards_count' => count($giftCardsGenerated),
                'invoice_id' => $invoiceTransactionId
            ]);
        }
        
        Log::info('Gift card generation completed', [
            'invoice_id' => $invoiceTransactionId,
            'gift_cards_generated' => count($giftCardsGenerated)
        ]);
    }
}
