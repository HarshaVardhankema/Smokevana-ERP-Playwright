<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Charts\CommonChart;
use App\Contact;
use App\Currency;
use App\Media;
use App\Product;
use App\Transaction;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\RestaurantUtil;
use App\Utils\TransactionUtil;
use App\Utils\ProductUtil;
use App\Utils\Util;
use App\VariationLocationDetails;
use Datatables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $businessUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    protected $commonUtil;

    protected $restUtil;
    protected $productUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil,
        Util $commonUtil,
        RestaurantUtil $restUtil,
        ProductUtil $productUtil,
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->user_type == 'user_customer') {
            return redirect()->action([\Modules\Crm\Http\Controllers\DashboardController::class, 'index']);
        }

        $business_id = request()->session()->get('user.business_id');

        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! auth()->user()->can('dashboard.data')) {
            return view('home.index');
        }

        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        //ensure start date starts from at least 30 days before to get sells last 30 days
        $least_30_days = \Carbon::parse($fy['start'])->subDays(30)->format('Y-m-d');

        //get all sells
        $sells_this_fy = $this->transactionUtil->getSellsCurrentFy($business_id, $least_30_days, $fy['end']);

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();

        // Set default location to BL0001 if it exists
        $default_location = null;
        foreach ($all_locations as $id => $name) {
            if (strpos($name, 'BL0001') !== false) {
                $default_location = (object)['id' => $id];
                break;
            }
        }

        //Chart for sells last 30 days
        $labels = [];
        $all_sell_values = [];
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            $labels[] = date('j M Y', strtotime($date));

            $total_sell_on_date = $sells_this_fy->where('date', $date)->sum('total_sells');

            if (! empty($total_sell_on_date)) {
                $all_sell_values[] = (float) $total_sell_on_date;
            } else {
                $all_sell_values[] = 0;
            }
        }

        //Group sells by location
        $location_sells = [];
        foreach ($all_locations as $loc_id => $loc_name) {
            $values = [];
            foreach ($dates as $date) {
                $total_sell_on_date_location = $sells_this_fy->where('date', $date)->where('location_id', $loc_id)->sum('total_sells');

                if (! empty($total_sell_on_date_location)) {
                    $values[] = (float) $total_sell_on_date_location;
                } else {
                    $values[] = 0;
                }
            }
            $location_sells[$loc_id]['loc_label'] = $loc_name;
            $location_sells[$loc_id]['values'] = $values;
        }

        $sells_chart_1 = new CommonChart;

        $sells_chart_1->labels($labels)
                        ->options($this->__chartOptions(__(
                            'home.total_sells',
                            ['currency' => $currency->code]
                            )));

        if (! empty($location_sells)) {
            foreach ($location_sells as $location_sell) {
                $sells_chart_1->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }

        if (count($all_locations) > 1) {
            $sells_chart_1->dataset(__('report.all_locations'), 'line', $all_sell_values);
        }

        $labels = [];
        $values = [];
        $date = strtotime($fy['start']);
        $last = date('m-Y', strtotime($fy['end']));
        $fy_months = [];
        do {
            $month_year = date('m-Y', $date);
            $fy_months[] = $month_year;

            $labels[] = \Carbon::createFromFormat('m-Y', $month_year)
                            ->format('M-Y');
            $date = strtotime('+1 month', $date);

            $total_sell_in_month_year = $sells_this_fy->where('yearmonth', $month_year)->sum('total_sells');

            if (! empty($total_sell_in_month_year)) {
                $values[] = (float) $total_sell_in_month_year;
            } else {
                $values[] = 0;
            }
        } while ($month_year != $last);

        $fy_sells_by_location_data = [];

        foreach ($all_locations as $loc_id => $loc_name) {
            $values_data = [];
            foreach ($fy_months as $month) {
                $total_sell_in_month_year_location = $sells_this_fy->where('yearmonth', $month)->where('location_id', $loc_id)->sum('total_sells');

                if (! empty($total_sell_in_month_year_location)) {
                    $values_data[] = (float) $total_sell_in_month_year_location;
                } else {
                    $values_data[] = 0;
                }
            }
            $fy_sells_by_location_data[$loc_id]['loc_label'] = $loc_name;
            $fy_sells_by_location_data[$loc_id]['values'] = $values_data;
        }

        $sells_chart_2 = new CommonChart;
        $sells_chart_2->labels($labels)
                    ->options($this->__chartOptions(__(
                        'home.total_sells',
                        ['currency' => $currency->code]
                            )));
        if (! empty($fy_sells_by_location_data)) {
            foreach ($fy_sells_by_location_data as $location_sell) {
                $sells_chart_2->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }
        if (count($all_locations) > 1) {
            $sells_chart_2->dataset(__('report.all_locations'), 'line', $values);
        }

        //Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');

        $widgets = [];

        foreach ($module_widgets as $widget_array) {
            if (! empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }

        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];


        return view('home.index', compact('sells_chart_1', 'sells_chart_2', 'widgets', 'all_locations', 'default_location', 'common_settings', 'is_admin'));
    }

    /**
     * Retrieves purchase and sell details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotals()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');

            // get user id parameter
            $created_by = request()->user_id;

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end, $location_id, $created_by);

            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start, $end, $location_id, $created_by);

            $total_ledger_discount = $this->transactionUtil->getTotalLedgerDiscount($business_id, $start, $end);

            $purchase_details['purchase_due'] = $purchase_details['purchase_due'] - $total_ledger_discount['total_purchase_discount'];

            $transaction_types = [
                'purchase_return', 'sell_return', 'expense',
            ];

            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start,
                $end,
                $location_id,
                $created_by
            );

            $total_purchase_inc_tax = ! empty($purchase_details['total_purchase_inc_tax']) ? $purchase_details['total_purchase_inc_tax'] : 0;
            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];

            $output = $purchase_details;
            $output['total_purchase'] = $total_purchase_inc_tax;
            $output['total_purchase_return'] = $total_purchase_return_inc_tax;
            $output['total_purchase_return_paid'] = $this->transactionUtil->getTotalPurchaseReturnPaid($business_id, $start, $end, $location_id);

            $total_sell_inc_tax = ! empty($sell_details['total_sell_inc_tax']) ? $sell_details['total_sell_inc_tax'] : 0;
            $total_sell_return_inc_tax = ! empty($transaction_totals['total_sell_return_inc_tax']) ? $transaction_totals['total_sell_return_inc_tax'] : 0;
            $output['total_sell_return_paid'] = $this->transactionUtil->getTotalSellReturnPaid($business_id, $start, $end, $location_id);

            $output['total_sell'] = $total_sell_inc_tax;
            $output['total_sell_return'] = $total_sell_return_inc_tax;

            $output['invoice_due'] = $sell_details['invoice_due'] - $total_ledger_discount['total_sell_discount'];
            $output['total_expense'] = $transaction_totals['total_expense'];

            //NET = TOTAL SALES - INVOICE DUE - EXPENSE
            $output['net'] = $output['total_sell'] - $output['invoice_due'] - $output['total_expense'];

            return $output;
        }
    }

    /**
     * Retrieves sell products whose available quntity is less than alert quntity.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductStockAlert()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $permitted_locations = auth()->user()->permitted_locations();
            $products = $this->productUtil->getProductAlert($business_id, $permitted_locations);

            return Datatables::of($products)
                ->editColumn('product', function ($row) {
                    if ($row->type == 'single') {
                        return $row->product.' ('.$row->sku.')';
                    } else {
                        return $row->product.' - '.$row->product_variation.' - '.$row->variation.' ('.$row->sub_sku.')';
                    }
                })
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;

                    return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>'.(float) $stock.'</span> '.$row->unit;
                })
                ->removeColumn('sku')
                ->removeColumn('sub_sku')
                ->removeColumn('unit')
                ->removeColumn('type')
                ->removeColumn('product_variation')
                ->removeColumn('variation')
                ->rawColumns([2])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format('Y-m-d H:i:s');

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->input('location_id'))) {
                $query->where('transactions.location_id', request()->input('location_id'));
            }

            $dues = $query->select(
                'transactions.id as id',
                'c.name as supplier',
                'c.supplier_business_name',
                'ref_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = ! empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;

                    return '<span class="display_currency" data-currency_symbol="true">'.
                    $due.'</span>';
                })
                ->addColumn('action', '@can("purchase.create") <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'addPayment\'], [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endcan')
                ->removeColumn('supplier_business_name')
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$supplier}}')
                ->editColumn('ref_no', function ($row) {
                    if (auth()->user()->can('purchase.view')) {
                        return  '<a href="#" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]).'"
                                    class="btn-modal" data-container=".view_modal">'.$row->ref_no.'</a>';
                    }

                    return $row->ref_no;
                })
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesPaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format('Y-m-d H:i:s');

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->input('location_id'))) {
                $query->where('transactions.location_id', request()->input('location_id'));
            }

            $dues = $query->select(
                'transactions.id as id',
                'c.name as customer',
                'c.supplier_business_name',
                'transactions.invoice_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = ! empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;

                    return '<span class="display_currency" data-currency_symbol="true">'.
                    $due.'</span>';
                })
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view')) {
                        return  '<a href="#" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]).'"
                                    class="btn-modal" data-container=".view_modal">'.$row->invoice_no.'</a>';
                    }

                    return $row->invoice_no;
                })
                ->addColumn('action', '@if(auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'addPayment\'], [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endif')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$customer}}')
                ->removeColumn('supplier_business_name')
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    public function loadMoreNotifications()
    {
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'DESC')->paginate(10);
        Log::info('Notifications Load More line 469', [$notifications]);
        if (request()->input('page') == 1) {
            Log::info('Notifications Load More line 472', [auth()->user()->unreadNotifications]);
            auth()->user()->unreadNotifications->markAsRead();
        }
        Log::info('Notifications Load More line 476', [$notifications]);
        $notifications_data = $this->commonUtil->parseNotifications($notifications);
        Log::info('Notifications Load More line 478', [$notifications_data]);
        return view('layouts.partials.notification_list', ['notifications_data' => $notifications_data]);
        // return view('layouts.partials.notification_list', compact('notifications_data'));
    }

    /**
     * Function to count total number of unread notifications
     *
     * @return json
     */
    public function getTotalUnreadNotifications()
    {
        $unread_notifications = auth()->user()->unreadNotifications;
        $total_unread = $unread_notifications->count();

        $notification_html = '';
        $modal_notifications = [];
        foreach ($unread_notifications as $unread_notification) {
            if (isset($data['show_popup'])) {
                $modal_notifications[] = $unread_notification;
                $unread_notification->markAsRead();
            }
        }
        if (! empty($modal_notifications)) {
            $notification_html = view('home.notification_modal')->with(['notifications' => $modal_notifications])->render();
        }

        return [
            'total_unread' => $total_unread,
            'notification_html' => $notification_html,
        ];
    }

    private function __chartOptions($title)
    {
        return [
            'yAxis' => [
                'title' => [
                    'text' => $title,
                ],
            ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical',
                'padding' => 20,
            ],
        ];
    }

    public function getCalendar()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->restUtil->is_admin(auth()->user(), $business_id);
        $is_superadmin = auth()->user()->can('superadmin');
        if (request()->ajax()) {
            $data = [
                'start_date' => request()->start,
                'end_date' => request()->end,
                'user_id' => ($is_admin || $is_superadmin) && ! empty(request()->user_id) ? request()->user_id : auth()->user()->id,
                'location_id' => ! empty(request()->location_id) ? request()->location_id : null,
                'business_id' => $business_id,
                'events' => request()->events ?? [],
                'color' => '#007FFF',
            ];
            $events = [];

            if (in_array('bookings', $data['events'])) {
                $events = $this->restUtil->getBookingsForCalendar($data);
            }

            $module_events = $this->moduleUtil->getModuleData('calendarEvents', $data);

            foreach ($module_events as $module_event) {
                $events = array_merge($events, $module_event);
            }

            return $events;
        }

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();
        $users = [];
        if ($is_admin) {
            $users = User::forDropdown($business_id, false);
        }

        $event_types = [
            'bookings' => [
                'label' => __('restaurant.bookings'),
                'color' => '#007FFF',
            ],
        ];
        $module_event_types = $this->moduleUtil->getModuleData('eventTypes');
        foreach ($module_event_types as $module_event_type) {
            $event_types = array_merge($event_types, $module_event_type);
        }

        return view('home.calendar')->with(compact('all_locations', 'users', 'event_types'));
    }

    public function showNotification($id)
    {
        $notification = DatabaseNotification::find($id);

        $data = $notification->data;

        $notification->markAsRead();

        return view('home.notification_modal')->with([
            'notifications' => [$notification],
        ]);
    }

    public function attachMediasToGivenModel(Request $request)
    {
        if ($request->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $model_id = $request->input('model_id');
                $model = $request->input('model_type');
                $model_media_type = $request->input('model_media_type');

                DB::beginTransaction();

                //find model to which medias are to be attached
                $model_to_be_attached = $model::where('business_id', $business_id)
                                        ->findOrFail($model_id);

                Media::uploadMedia($business_id, $model_to_be_attached, $request, 'file', false, $model_media_type);

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (Exception $e) {
                DB::rollBack();

                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function getUserLocation($latlng)
    {
        $latlng_array = explode(',', $latlng);

        $response = $this->moduleUtil->getLocationFromCoordinates($latlng_array[0], $latlng_array[1]);

        return ['address' => $response];
    }
    public function infoPage(){
        return view('info.info');
    }
    public function navigationPage()
    {
        if(!auth()->user()->can('navigation_page_access')||auth()->user()->can('access_all_locations')){
            return redirect()->route('home')->with('error', 'You are not authorized to access this page');
        }
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->get();
        return view('home.navigation_page', compact('business_locations'));
    }

    /**
     * Searchable sidebar menu entries (label, url) with permission checks.
     * Used by search() to allow finding and navigating to menu pages.
     */
    private function getSearchableMenuEntries()
    {
        $entries = [];
        $enabled_modules = (array) (session('business.enabled_modules') ?? []);
        $pos_settings = json_decode(session('business.pos_settings') ?? '{}', true) ?? [];
        $is_admin = auth()->user()->hasRole('Admin#' . session('business.id'));

        $entries[] = ['title' => __('home.home'), 'url' => action([self::class, 'index'])];
        if (auth()->user()->can('product.create')) {
            $entries[] = ['title' => __('product.add_product'), 'url' => action([\App\Http\Controllers\ProductController::class, 'create'])];
        }
        if (auth()->user()->can('product.view')) {
            $entries[] = ['title' => __('lang_v1.list_products'), 'url' => action([\App\Http\Controllers\ProductController::class, 'index'])];
        }
        if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
            $entries[] = ['title' => 'Add Sale Invoice', 'url' => action([\App\Http\Controllers\SellController::class, 'create'])];
        }
        if (!empty($pos_settings['enable_sales_order']) && ($is_admin || auth()->user()->hasAnyPermission(['so.view_own', 'so.view_all', 'so.create']))) {
            $entries[] = ['title' => __('lang_v1.add_sales_order'), 'url' => action([\App\Http\Controllers\SellController::class, 'create']) . '?sale_type=sales_order'];
            $entries[] = ['title' => 'Sales Order', 'url' => action([\App\Http\Controllers\SalesOrderController::class, 'index'])];
            $entries[] = ['title' => 'Manage Order', 'url' => url('/order-fulfillment')];
        }
        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
            $entries[] = ['title' => 'Sales Invoice', 'url' => action([\App\Http\Controllers\SellController::class, 'index'])];
        }
        if (auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own')) {
            $entries[] = ['title' => __('report.customer'), 'url' => action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer'])];
        }
        if (auth()->user()->can('supplier.view') || auth()->user()->can('supplier.view_own')) {
            $entries[] = ['title' => 'Vendor', 'url' => action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'supplier'])];
        }
        if (auth()->user()->can('supplier.create') || auth()->user()->can('customer.create')) {
            $entries[] = ['title' => __('contact.add_contact'), 'url' => url('contacts?type=customer&open_create=1')];
        }
        if (in_array('purchases', $enabled_modules)) {
            if (auth()->user()->can('purchase.create')) {
                $entries[] = ['title' => __('purchase.add_purchase'), 'url' => action([\App\Http\Controllers\PurchaseController::class, 'create'])];
            }
            if (auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase')) {
                $entries[] = ['title' => __('purchase.list_purchase'), 'url' => action([\App\Http\Controllers\PurchaseController::class, 'index'])];
            }
        }
        if (auth()->user()->can('category.view') || auth()->user()->can('category.create')) {
            $entries[] = ['title' => __('category.categories'), 'url' => action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=product'];
        }
        if (auth()->user()->can('category.create')) {
            $entries[] = ['title' => __('category.add_category'), 'url' => url('taxonomies?type=product&open_create=1')];
        }
        if (auth()->user()->can('brand.view') || auth()->user()->can('brand.create')) {
            $entries[] = ['title' => __('brand.brands'), 'url' => action([\App\Http\Controllers\BrandController::class, 'index'])];
        }
        if (auth()->user()->can('brand.create')) {
            $entries[] = ['title' => __('brand.add_brand'), 'url' => url('brands?open_create=1')];
        }
        if (auth()->user()->can('product.update')) {
            $entries[] = ['title' => 'Edit Selling Price', 'url' => action([\App\Http\Controllers\ProductController::class, 'editSellingPrice'])];
        }
        if (in_array('expenses', $enabled_modules) && auth()->user()->can('expense.add')) {
            $entries[] = ['title' => __('expense.add_expense'), 'url' => action([\App\Http\Controllers\ExpenseController::class, 'create'])];
            $entries[] = ['title' => __('lang_v1.list_expenses'), 'url' => action([\App\Http\Controllers\ExpenseController::class, 'index'])];
        }
        if (auth()->user()->can('profit_loss_report.view')) {
            $entries[] = ['title' => __('report.profit_loss'), 'url' => action([\App\Http\Controllers\ReportController::class, 'getProfitLoss'])];
        }
        if (auth()->user()->can('stock_report.view')) {
            $entries[] = ['title' => __('report.stock_report'), 'url' => action([\App\Http\Controllers\ReportController::class, 'getStockReport'])];
        }
        if (auth()->user()->can('account.access') && in_array('account', $enabled_modules)) {
            $entries[] = ['title' => __('account.list_accounts'), 'url' => action([\App\Http\Controllers\AccountController::class, 'index'])];
        }
        if (auth()->user()->can('user.view')) {
            $entries[] = ['title' => __('user.users'), 'url' => action([\App\Http\Controllers\ManageUserController::class, 'index'])];
        }
        if (auth()->user()->can('roles.view')) {
            $entries[] = ['title' => __('user.roles'), 'url' => action([\App\Http\Controllers\RoleController::class, 'index'])];
        }
        if (auth()->user()->can('business_settings.access')) {
            $entries[] = ['title' => __('business.business_settings'), 'url' => action([\App\Http\Controllers\BusinessController::class, 'getBusinessSettings'])];
        }
        $entries[] = ['title' => 'Discount', 'url' => action([\App\Http\Controllers\CustomDiscountController::class, 'index'])];

        return $entries;
    }

    /**
     * Search across transactions, contacts, products, and sidebar menu pages
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $business_id = request()->session()->get('user.business_id');
        $results = [];
        $q = mb_strtolower($query);

        // Search sidebar menu modules (e.g. "add product" -> Add Product page)
        $menuEntries = $this->getSearchableMenuEntries();
        foreach ($menuEntries as $e) {
            if (str_contains(mb_strtolower($e['title']), $q)) {
                $results[] = ['title' => $e['title'], 'subtitle' => __('home.menu_page'), 'url' => $e['url']];
                if (count($results) >= 8) {
                    break;
                }
            }
        }

        // Search transactions (invoices/sales orders)
        $transactions = Transaction::where('business_id', $business_id)
            ->where(function($q) use ($query) {
                $q->where('invoice_no', 'like', "%{$query}%")
                  ->orWhere('ref_no', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        foreach ($transactions as $transaction) {
            $typeLabel = $transaction->type == 'sales_order' ? 'Sales Order' : 'Invoice';
            $results[] = [
                'title' => $typeLabel . ': ' . $transaction->invoice_no,
                'subtitle' => 'Date: ' . \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d') . ' | Total: ' . number_format($transaction->final_total, 2),
                'url' => action([\App\Http\Controllers\SellController::class, 'show'], [$transaction->id])
            ];
        }

        // Search contacts (customers/suppliers)
        $contacts = Contact::where('business_id', $business_id)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('supplier_business_name', 'like', "%{$query}%")
                  ->orWhere('contact_id', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('mobile', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        foreach ($contacts as $contact) {
            $typeLabel = ucfirst($contact->type ?? 'Contact');
            $name = $contact->supplier_business_name ?? $contact->name;
            $results[] = [
                'title' => $typeLabel . ': ' . $name,
                'subtitle' => ($contact->contact_id ? 'ID: ' . $contact->contact_id . ' | ' : '') . ($contact->email ? 'Email: ' . $contact->email : ''),
                'url' => action([\App\Http\Controllers\ContactController::class, 'show'], [$contact->id])
            ];
        }

        // Search products
        $products = Product::where('business_id', $business_id)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        foreach ($products as $product) {
            $results[] = [
                'title' => 'Product: ' . $product->name,
                'subtitle' => ($product->sku ? 'SKU: ' . $product->sku . ' | ' : '') . 'Type: ' . ucfirst($product->type ?? 'single'),
                'url' => action([\App\Http\Controllers\ProductController::class, 'edit'], [$product->id])
            ];
        }

        return response()->json(['results' => $results]);
    }
}
