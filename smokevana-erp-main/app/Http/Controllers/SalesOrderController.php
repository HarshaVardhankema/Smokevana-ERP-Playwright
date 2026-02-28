<?php

namespace App\Http\Controllers;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\InvoiceScheme;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TypesOfService;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    protected $transactionUtil;

    protected $businessUtil;

    protected $commonUtil;

    protected $contactUtil;

    protected $moduleUtil;

    protected $dummyPaymentLine = [
        'method' => 'cash',
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

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, BusinessUtil $businessUtil, Util $commonUtil, ContactUtil $contactUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->commonUtil = $commonUtil;
        $this->contactUtil = $contactUtil;
        $this->moduleUtil = $moduleUtil;
        $this->sales_order_statuses = [
              'pending' => [
                'label' => __('lang_v1.pending'),
                'class' => 'bg-yellow',
            ],
            'ordered' => [
                'label' => __('lang_v1.ordered'),
                'class' => 'bg-info',
            ],
            'partial' => [
                'label' => __('lang_v1.partial'),
                'class' => 'bg-yellow',
                'class' => 'bg-orange',
            ],
            'completed' => [
                'label' => __('restaurant.completed'),
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'class' => 'bg-green',
                'class' => 'bg-red',
            ],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('so.view_own') && ! auth()->user()->can('so.view_all') && ! auth()->user()->can('so.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $sales_order_statuses = [];
        foreach ($this->sales_order_statuses as $key => $value) {
            $sales_order_statuses[$key] = $value['label'];
        }

        return view('sales_order.index')
            ->with(compact('business_locations', 'customers', 'shipping_statuses', 'sales_order_statuses'));
    }

    /**
     * Show the form for creating a new sales order.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('so.create')) {
            abort(403, 'Unauthorized action.');
        }

        $cid = request()->query('cid');
        if ($cid) {
            $customer = Contact::find($cid);
            if ($customer && $customer->contact_status == 'inactive') {
                return back()->with('status', [
                    'success' => 0,
                    'msg' => "Customer is deactivated",
                ]);
            }
        }

        $business_id = request()->session()->get('user.business_id');

        // Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (! $this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action([\App\Http\Controllers\SalesOrderController::class, 'index']));
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

        // Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $default_price_group_id = ! empty($default_location->selling_price_group_id) && array_key_exists($default_location->selling_price_group_id, $price_groups) ? $default_location->selling_price_group_id : null;

        $default_datetime = $this->businessUtil->format_date('now', true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        // Permissions for editing discount & price
        $edit_discount = auth()->user()->can('edit_product_discount_from_pos_screen');
        $edit_price = auth()->user()->can('edit_product_price_from_pos_screen');

        $invoice_schemes = InvoiceScheme::forDropdown($business_id);
        $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        if (! empty($default_location) && ! empty($default_location->sale_invoice_scheme_id)) {
            $default_invoice_schemes = InvoiceScheme::where('business_id', $business_id)
                ->findorfail($default_location->sale_invoice_scheme_id);
        }
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        // Types of service
        $types_of_service = [];
        if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
            $types_of_service = TypesOfService::forDropdown($business_id);
        }

        // Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }

        $status = 'ordered';
        $sale_type = 'sales_order';
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

    public function getSalesOrders($customer_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $location_id = request()->input('location_id');

        $sales_orders = Transaction::where('business_id', $business_id)
                            ->where('location_id', $location_id)
                            ->where('type', 'sales_order')
                            ->whereIn('status', ['partial', 'ordered'])
                            ->where('contact_id', $customer_id)
                            ->select('invoice_no as text', 'id')
                            ->get();

        return $sales_orders;
    }

    /**
     * get required resources
     *
     * to edit sales order status
     *
     * @return \Illuminate\Http\Response
     */
    public function getEditSalesOrderStatus(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $transaction = Transaction::where('business_id', $business_id)
                                ->findOrFail($id);

            $status = $transaction->status;
            $statuses = $this->sales_order_statuses;

            return view('sales_order.edit_status_modal')
                ->with(compact('id', 'status', 'statuses'));
        }
    }

    /**
     * updare sales order status
     *
     * @return \Illuminate\Http\Response
     */
    public function postEditSalesOrderStatus(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $transaction = Transaction::where('business_id', $business_id)
                                ->findOrFail($id);

                $transaction_before = $transaction->replicate();

                $transaction->status = $request->input('status');
                $transaction->save();

                $activity_property = ['from' => $transaction_before->status, 'to' => $request->input('status')];
                $this->commonUtil->activityLog($transaction, 'status_updated', $transaction_before, $activity_property);

                $output = [
                    'success' => 1,
                    'msg' => trans('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                $output = [
                    'success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
}
