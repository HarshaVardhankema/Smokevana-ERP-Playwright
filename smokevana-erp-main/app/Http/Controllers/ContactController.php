<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessIdentification;
use App\BusinessLocation;
use App\Brands;
use App\Cart;
use App\CartItem;
use App\Complaint;
use App\Contact;
use App\CustomerAddress;
use App\CustomerGroup;
use App\CustomerPriceRecall;
use App\DocumentAndNote;
use App\Notifications\CustomerNotification;
use App\PurchaseLine;
use App\Transaction;
use App\TransactionPayment;
use App\TrashSource;
use App\User;
use App\UserContactAccess;
use App\Wishlist;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use App\Events\ContactCreatedOrModified;
use App\LocationTaxCharge;
use App\Exports\ContactsExport;
use App\Product;
use App\Variation;
use App\Models\WpVendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    protected $commonUtil;

    protected $contactUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    protected $notificationUtil;

    /**
     * Constructor
     *
     * 
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil,
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        NotificationUtil $notificationUtil,
        ContactUtil $contactUtil
    ) {
        $this->commonUtil = $commonUtil;
        $this->contactUtil = $contactUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->notificationUtil = $notificationUtil;
    }

    /**
     * Get location for contact creation/update
     * Auto-detect for regular users, allow selection for super admins
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int|null
     */
    public function getLocationForContact($request)
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
            // If no location selected by super admin, return null to show all contacts
            return null;
        }
        
        // For regular users, auto-detect location
        $permitted_locations = $user->permitted_locations($business_id);
        
        if ($permitted_locations == 'all') {
            // User has access to all locations, get first available location
            $default_location = BusinessLocation::where('business_id', $business_id)
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        $type = request()->get('type');

        $types = ['supplier', 'customer'];

        if (empty($type) || ! in_array($type, $types)) {
            return redirect()->back();
        }

        if (request()->ajax()) {
            if ($type == 'supplier') {
                return $this->indexSupplier();
            } elseif ($type == 'customer') {
                return $this->indexCustomer();
            } else {
                exit('Not Found');
            }
        }

        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && in_array($type, ['customer'])) ? true : false;

        $users = User::forDropdown($business_id);

        $customer_groups = [];
        $customer_status_counts = [];
        $prime_customer_counts = [];
        $vendor_type_counts = [];

        if ($type == 'customer') {
            $customer_groups = CustomerGroup::forDropdown($business_id);
        }

        // Get business locations for filters
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        
        // Get brands for filters (will be loaded dynamically based on location)
        $brands = [];

        if ($type == 'customer') {
            $customer_status_counts = $this->getCustomerStatusCounts($business_id);
            $prime_customer_counts = $this->getPrimeCustomerCounts($business_id);
        } elseif ($type == 'supplier') {
            $vendor_type_counts = $this->getVendorTypeCounts($business_id);
        }

        return view('contact.index')
            ->with(compact(
                'type',
                'reward_enabled',
                'customer_groups',
                'users',
                'business_locations',
                'brands',
                'customer_status_counts',
                'prime_customer_counts',
                'vendor_type_counts'
            ));
    }

    /**
     * Returns the database object for supplier
     *
     * @return \Illuminate\Http\Response
     */
    private function indexSupplier()
    {
        if (! auth()->user()->can('supplier.view') && ! auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = $this->getLocationForContact(request());

        $contact = $this->contactUtil->getContactQuery($business_id, 'supplier', [], $location_id);

        if (request()->has('has_purchase_due')) {
            $contact->havingRaw('(total_purchase - purchase_paid) > 0');
        }

        if (request()->has('has_purchase_return')) {
            $contact->havingRaw('total_purchase_return > 0');
        }

        if (request()->has('has_advance_balance')) {
            $contact->where('balance', '>', 0);
        }

        if (request()->has('has_opening_balance')) {
            $contact->havingRaw('opening_balance > 0');
        }

        if (! empty(request()->input('contact_status'))) {
            $contact->where('contacts.contact_status', request()->input('contact_status'));
        }

        if (! empty(request()->input('assigned_to'))) {
            $contact->join('user_contact_access AS uc', 'contacts.id', 'uc.contact_id')
                ->where('uc.user_id', request()->input('assigned_to'));
        }

        return Datatables::of($contact)
            ->addColumn('row_select', function ($row) {
                return '<input type="checkbox" class="row-select" value="' . $row->id . '">';
            })
            ->addColumn('address', function ($row) {
                $full_address = implode(", ", array_filter([
                    $row->address_line_1,
                    $row->address_line_2,
                    $row->city,
                    $row->state,
                    $row->country,
                    $row->zip_code,
                ]));

                return Str::limit($full_address, 30);
            })
            ->addColumn(
                'due',
                '<span class="contact_due" data-orig-value="{{$total_purchase - $purchase_paid - $total_ledger_discount}}" data-highlight=false>@format_currency($total_purchase - $purchase_paid - $total_ledger_discount)</span>'
            )
            ->addColumn(
                'return_due',
                '<span class="return_due" data-orig-value="{{$total_purchase_return - $purchase_return_paid}}" data-highlight=false>@format_currency($total_purchase_return - $purchase_return_paid)'
            )
            ->addColumn(
                'action',
                function ($row) {
                    $html = '<div class="btn-group dropdown scroll-safe-dropdown">
                    <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max  dropdown-toggle" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __('messages.actions') .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    $html .= '<li><a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$row->id]) . '?type=purchase" class="pay_purchase_due"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>' . __('lang_v1.pay') . '</a></li>';

                    $return_due = $row->total_purchase_return - $row->purchase_return_paid;
                    if ($return_due > 0) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$row->id]) . '?type=purchase_return" class="pay_purchase_due"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>' . __('lang_v1.receive_purchase_return_due') . '</a></li>';
                    }

                    if (auth()->user()->can('supplier.view') || auth()->user()->can('supplier.view_own')) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id, 'type=supplier']) . '"><i class="fas fa-eye" aria-hidden="true"></i>' . __('messages.view') . '</a></li>';
                    }
                    if (auth()->user()->can('supplier.update')) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'edit'], [$row->id]) . '" class="edit_contact_button"><i class="fas fa-edit" aria-hidden="true"></i>' . __('messages.edit') . '</a></li>';
                    }
                    if (auth()->user()->can('supplier.delete')) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'destroy'], [$row->id]) . '" class="delete_contact_button"><i class="fas fa-trash" aria-hidden="true"></i>' . __('messages.delete') . '</a></li>';
                    }
                    // Add approve/reject buttons for suppliers
                    if (! $row->is_default && auth()->user()->can('supplier.update')) {
                        if ($row->isApproved == 0 || $row->isApproved == '' || $row->isApproved == null) {
                            $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'approve'], [$row->id]) . '" class="approve_contact_button"><i class="fas fa-check-square"></i>' . __('messages.approve') . '</a></li>';
                        }
                        if ($row->isApproved == 1 || $row->isApproved == '' || $row->isApproved == null) {
                            $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'notApprove'], [$row->id]) . '" class="not_approve_contact_button"><i class="fa fa-ban" aria-hidden="true"></i>' . __('messages.reject') . '</a></li>';
                        }
                    }

                    if (auth()->user()->can('customer.update')) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'updateStatus'], [$row->id]) . '"class="update_contact_status"><i class="fas fa-power-off"></i>';

                        if ($row->contact_status == 'active') {
                            $html .= __('messages.deactivate');
                        } else {
                            $html .= __('messages.activate');
                        }

                        $html .= '</a></li>';
                    }

                    $html .= '<li class="divider"></li>';
                    if (auth()->user()->can('supplier.view')) {
                        $html .= '
                                <li>
                                    <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=ledger">
                                        <i class="fas fa-scroll" aria-hidden="true"></i>
                                        ' . __('lang_v1.ledger') . '
                                    </a>
                                </li>';

                        if (in_array($row->type, ['both', 'supplier'])) {
                            $html .= '<li>
                                <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=purchase">
                                    <i class="fas fa-arrow-circle-down" aria-hidden="true"></i>
                                    ' . __('purchase.purchases') . '
                                </a>
                            </li>
                            <li>
                                <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=stock_report">
                                    <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                                    ' . __('report.stock_report') . '
                                </a>
                            </li>';
                        }

                        if (in_array($row->type, ['both', 'customer'])) {
                            $html .= '<li>
                                <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=sales">
                                    <i class="fas fa-arrow-circle-up" aria-hidden="true"></i>
                                    ' . __('sale.sells') . '
                                </a>
                            </li>';
                        }

                        $html .= '<li>
                                <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=documents_and_notes">
                                    <i class="fas fa-paperclip" aria-hidden="true"></i>
                                     ' . __('lang_v1.documents_and_notes') . '
                                </a>
                            </li>';
                    }
                    $html .= '</ul></div>';

                    return $html;
                }
            )
            ->editColumn('opening_balance', function ($row) {
                $html = '<span data-orig-value="' . $row->opening_balance . '">' . $this->transactionUtil->num_f($row->opening_balance, true) . '</span>';

                return $html;
            })
            ->editColumn('balance', function ($row) {
                $html = '<span data-orig-value="' . $row->balance . '">' . $this->transactionUtil->num_f($row->balance, true) . '</span>';

                return $html;
            })
            ->editColumn('pay_term', '
                @if(!empty($pay_term_type) && !empty($pay_term_number))
                    {{$pay_term_number}}
                    @lang("lang_v1.".$pay_term_type)
                @endif
            ')
            ->editColumn('name', function ($row) {
                if ($row->contact_status == 'inactive') {
                    return $row->name . ' <small class="label pull-right bg-red no-print">' . __('lang_v1.inactive') . '</small>';
                } else {
                    return $row->name;
                }
            })
            ->editColumn('created_at', '{{@format_date($created_at)}}')
            ->removeColumn('opening_balance_paid')
            ->removeColumn('type')
            ->removeColumn('id')
            ->removeColumn('total_purchase')
            ->removeColumn('purchase_paid')
            ->removeColumn('total_purchase_return')
            ->removeColumn('purchase_return_paid')
            ->filterColumn('address', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('address_line_1', 'like', "%{$keyword}%")
                        ->orWhere('address_line_2', 'like', "%{$keyword}%")
                        ->orWhere('city', 'like', "%{$keyword}%")
                        ->orWhere('state', 'like', "%{$keyword}%")
                        ->orWhere('country', 'like', "%{$keyword}%")
                        ->orWhere('zip_code', 'like', "%{$keyword}%")
                        ->orWhereRaw("CONCAT(COALESCE(address_line_1, ''), ', ', COALESCE(address_line_2, ''), ', ', COALESCE(city, ''), ', ', COALESCE(state, ''), ', ', COALESCE(country, '') ) like ?", ["%{$keyword}%"]);
                });
            })->setRowAttr([
                'href' => function ($row) {
                    if (auth()->user()->can('customer.view')) {
                        return  action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id, 'type=supplier']);
                    } else {
                        return '';
                    }
                },
            ])

            ->rawColumns(['row_select', 'action', 'opening_balance', 'pay_term', 'due', 'return_due', 'name', 'balance'])
            ->make(true);
    }

    /**
     * Get customer status counts for all statuses
     *
     * @param int $business_id
     * @return array
     */
    private function getCustomerStatusCounts($business_id)
    {
        // Respect explicit location filter first (like customer list)
        $filter_location_id = request()->input('location_id');
        $location_id = !empty($filter_location_id)
            ? $filter_location_id
            : $this->getLocationForContact(request());

        // Base query for customers (using same logic as getContactQuery)
        $baseQuery = Contact::where('contacts.business_id', $business_id)
            ->onlyCustomers();

        // Apply location filter if provided
        if (!empty($location_id)) {
            $baseQuery->where('contacts.location_id', $location_id);
        }
        
        // Count Active customers (contact_status = 'active' AND not guest)
        $activeCount = (clone $baseQuery)
            ->where('contacts.contact_status', 'active')
            ->where(function($q) {
                $q->where('contacts.is_guest', false)
                  ->orWhereNull('contacts.is_guest');
            })
            ->count();
        
        // Count Inactive customers (contact_status = 'inactive' AND not guest)
        $inactiveCount = (clone $baseQuery)
            ->where('contacts.contact_status', 'inactive')
            ->where(function($q) {
                $q->where('contacts.is_guest', false)
                  ->orWhereNull('contacts.is_guest');
            })
            ->count();
        
        // Count Pending customers (isApproved IS NULL AND not guest)
        $pendingCount = (clone $baseQuery)
            ->whereNull('contacts.isApproved')
            ->where(function($q) {
                $q->where('contacts.is_guest', false)
                  ->orWhereNull('contacts.is_guest');
            })
            ->count();
        
        // Count Rejected customers (isApproved = 0 AND not guest)
        $rejectedCount = (clone $baseQuery)
            ->where('contacts.isApproved', 0)
            ->where(function($q) {
                $q->where('contacts.is_guest', false)
                  ->orWhereNull('contacts.is_guest');
            })
            ->count();
        
        // Count Guest customers (is_guest = true)
        $guestCount = (clone $baseQuery)
            ->where('contacts.is_guest', true)
            ->count();
        
        return [
            'active' => $activeCount,
            'inactive' => $inactiveCount,
            'pending' => $pendingCount,
            'rejected' => $rejectedCount,
            'guest' => $guestCount,
        ];
    }

    /**
     * Get vendor type counts (in-house vs dropship suppliers).
     *
     * @param int $business_id
     * @return array
     */
    private function getVendorTypeCounts($business_id)
    {
        // Respect explicit location filter first (like supplier list)
        $filter_location_id = request()->input('location_id');
        $location_id = !empty($filter_location_id)
            ? $filter_location_id
            : $this->getLocationForContact(request());

        // Base query for suppliers (reuse same logic as supplier listing)
        $baseQuery = Contact::where('contacts.business_id', $business_id)
            ->onlySuppliers();

        if (!empty($location_id)) {
            $baseQuery->where('contacts.location_id', $location_id);
        }

        // In-house vendors = normal vendor_type
        $inhouseCount = (clone $baseQuery)
            ->where('contacts.vendor_type', 'normal')
            ->count();

        // Dropship vendors = dropshipping vendor_type
        $dropshipCount = (clone $baseQuery)
            ->where('contacts.vendor_type', 'dropshipping')
            ->count();

        return [
            'inhouse' => $inhouseCount,
            'dropship' => $dropshipCount,
        ];
    }

    /**
     * Get Prime customer tier counts
     *
     * @param int $business_id
     * @return array
     */
    private function getPrimeCustomerCounts($business_id)
    {
        // Respect explicit location filter first (like customer list)
        $filter_location_id = request()->input('location_id');
        $location_id = !empty($filter_location_id)
            ? $filter_location_id
            : $this->getLocationForContact(request());

        // Prime tier names to search for (with variations)
        $primeTiers = [
            'prime_silver' => ['Prime Silver', 'prime silver', 'PRIME SILVER'],
            'prime_gold' => ['Prime Gold', 'prime gold', 'PRIME GOLD'],
            'prime_platinum' => ['Prime Platinum', 'prime platinum', 'PRIME PLATINUM'],
            'prime_elite' => ['Prime Elite', 'prime elite', 'PRIME ELITE'],
            'prime_pro' => ['Prime Pro', 'prime pro', 'PRIME PRO'],
            'prime_pro_max' => ['Prime Pro Max', 'prime pro max', 'PRIME PRO MAX', 'Prime ProMax', 'Prime Pro-Max']
        ];

        $primeCounts = [];

        foreach ($primeTiers as $key => $tierVariations) {
            $customerGroupIds = [];

            // Search for customer groups matching any variation of the tier name
            foreach ($tierVariations as $tierName) {
                $groupIds = \App\CustomerGroup::where('business_id', $business_id)
                    ->where(function ($q) use ($tierName) {
                        $q->where('name', 'like', '%' . $tierName . '%')
                          ->orWhere('name', 'like', '%' . str_replace(' ', '', $tierName) . '%')
                          ->orWhere('name', 'like', '%' . str_replace(' ', '-', $tierName) . '%');
                    })
                    ->pluck('id')
                    ->toArray();

                $customerGroupIds = array_merge($customerGroupIds, $groupIds);
            }

            // Remove duplicates
            $customerGroupIds = array_unique($customerGroupIds);

            if (empty($customerGroupIds)) {
                $primeCounts[$key] = 0;
                continue;
            }

            // Base query for customers
            $query = Contact::where('contacts.business_id', $business_id)
                ->onlyCustomers()
                ->whereIn('contacts.customer_group_id', $customerGroupIds);

            // Apply location filter if provided
            if (!empty($location_id)) {
                $query->where('contacts.location_id', $location_id);
            }

            $count = $query->count();
            $primeCounts[$key] = $count;
        }

        return $primeCounts;
    }

    /**
     * Get customer group IDs for a single Prime tier (for filtering customer list).
     *
     * @param int $business_id
     * @param string $prime_tier Key e.g. prime_platinum, prime_silver
     * @return array
     */
    private function getPrimeTierCustomerGroupIds($business_id, $prime_tier)
    {
        $primeTiers = [
            'prime_silver' => ['Prime Silver', 'prime silver', 'PRIME SILVER'],
            'prime_gold' => ['Prime Gold', 'prime gold', 'PRIME GOLD'],
            'prime_platinum' => ['Prime Platinum', 'prime platinum', 'PRIME PLATINUM'],
            'prime_elite' => ['Prime Elite', 'prime elite', 'PRIME ELITE'],
            'prime_pro' => ['Prime Pro', 'prime pro', 'PRIME PRO'],
            'prime_pro_max' => ['Prime Pro Max', 'prime pro max', 'PRIME PRO MAX', 'Prime ProMax', 'Prime Pro-Max']
        ];

        if (!isset($primeTiers[$prime_tier])) {
            return [];
        }

        $customerGroupIds = [];
        foreach ($primeTiers[$prime_tier] as $tierName) {
            $groupIds = CustomerGroup::where('business_id', $business_id)
                ->where(function ($q) use ($tierName) {
                    $q->where('name', 'like', '%' . $tierName . '%')
                      ->orWhere('name', 'like', '%' . str_replace(' ', '', $tierName) . '%')
                      ->orWhere('name', 'like', '%' . str_replace(' ', '-', $tierName) . '%');
                })
                ->pluck('id')
                ->toArray();
            $customerGroupIds = array_merge($customerGroupIds, $groupIds);
        }

        return array_values(array_unique($customerGroupIds));
    }

    /**
     * Return customer status & prime tier counts as JSON (for live updates)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerCounts(Request $request)
    {
        if (!auth()->user()->can('customer.view') && !auth()->user()->can('customer.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $status_counts = $this->getCustomerStatusCounts($business_id);
        $prime_counts = $this->getPrimeCustomerCounts($business_id);

        return response()->json([
            'success' => true,
            'status_counts' => $status_counts,
            'prime_counts' => $prime_counts,
        ]);
    }

    /**
     * Returns the database object for customer
     *
     * @return \Illuminate\Http\Response
     */
    private function indexCustomer()
    {
        if (! auth()->user()->can('customer.view') && ! auth()->user()->can('customer.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        // Get location_id from filter if provided, otherwise use auto-detected
        $filter_location_id = request()->input('location_id');
        $location_id = !empty($filter_location_id) ? $filter_location_id : $this->getLocationForContact(request());

        $is_admin = $this->contactUtil->is_admin(auth()->user());

        $query = $this->contactUtil->getContactQuery($business_id, 'customer', [], $location_id);

        if (request()->has('has_sell_due')) {
            $query->havingRaw('(total_invoice - invoice_received) > 0');
        }
        // tab filter 
        if ($status = request()->get('contact_status_tab_filter')) {
            switch ($status) {
                case 'active':
                    $query->havingRaw('contact_status = "active"')
                          ->where(function($q) {
                              $q->where('contacts.is_guest', false)
                                ->orWhereNull('contacts.is_guest');
                          });
                    break;

                case 'inactive':
                    $query->havingRaw('contact_status = "inactive"')
                          ->where(function($q) {
                              $q->where('contacts.is_guest', false)
                                ->orWhereNull('contacts.is_guest');
                          });
                    break;

                case 'pending':
                    $query->havingRaw('isApproved IS NULL')
                          ->where(function($q) {
                              $q->where('contacts.is_guest', false)
                                ->orWhereNull('contacts.is_guest');
                          });
                    break;

                case 'rejected':
                    $query->havingRaw('isApproved = 0')
                          ->where(function($q) {
                              $q->where('contacts.is_guest', false)
                                ->orWhereNull('contacts.is_guest');
                          });
                    break;

                case 'guest':
                    $query->where('contacts.is_guest', true);
                    break;
            }
        } else {
            // Default (active tab) - exclude guest customers
            $query->where(function($q) {
                $q->where('contacts.is_guest', false)
                  ->orWhereNull('contacts.is_guest');
            });
        }


        if (request()->has('has_sell_return')) {
            $query->havingRaw('total_sell_return > 0');
        }

        if (request()->has('has_advance_balance')) {
            $query->where('balance', '>', 0);
        }

        if (request()->has('has_opening_balance')) {
            $query->havingRaw('opening_balance > 0');
        }

        if (! empty(request()->input('assigned_to'))) {
            $query->join('user_contact_access AS uc', 'contacts.id', 'uc.contact_id')
                ->where('uc.user_id', request()->input('assigned_to'));
        }

        $has_no_sell_from = request()->input('has_no_sell_from', null);

        if (
            (! $is_admin && auth()->user()->can('customer_with_no_sell_one_month')) ||
            ($has_no_sell_from == 'one_month' && (auth()->user()->can('customer_with_no_sell_one_month') || auth()->user()->can('customer_irrespective_of_sell')))
        ) {
            $from_transaction_date = \Carbon::now()->subDays(30)->format('Y-m-d');
            $query->havingRaw("max_transaction_date < '{$from_transaction_date}'")
                ->orHavingRaw('transaction_date IS NULL');
        }

        if (
            (! $is_admin && auth()->user()->can('customer_with_no_sell_three_month')) ||
            ($has_no_sell_from == 'three_months' && (auth()->user()->can('customer_with_no_sell_three_month') || auth()->user()->can('customer_irrespective_of_sell')))
        ) {
            $from_transaction_date = \Carbon::now()->subMonths(3)->format('Y-m-d');
            $query->havingRaw("max_transaction_date < '{$from_transaction_date}'")
                ->orHavingRaw('transaction_date IS NULL');
        }

        if (
            (! $is_admin && auth()->user()->can('customer_with_no_sell_six_month')) ||
            ($has_no_sell_from == 'six_months' && (auth()->user()->can('customer_with_no_sell_six_month') || auth()->user()->can('customer_irrespective_of_sell')))
        ) {
            $from_transaction_date = \Carbon::now()->subMonths(6)->format('Y-m-d');
            $query->havingRaw("max_transaction_date < '{$from_transaction_date}'")
                ->orHavingRaw('transaction_date IS NULL');
        }

        if ((! $is_admin && auth()->user()->can('customer_with_no_sell_one_year')) ||
            ($has_no_sell_from == 'one_year' && (auth()->user()->can('customer_with_no_sell_one_year') || auth()->user()->can('customer_irrespective_of_sell')))
        ) {
            $from_transaction_date = \Carbon::now()->subYear()->format('Y-m-d');
            $query->havingRaw("max_transaction_date < '{$from_transaction_date}'")
                ->orHavingRaw('transaction_date IS NULL');
        }

        if (! empty(request()->input('customer_group_id'))) {
            $query->where('contacts.customer_group_id', request()->input('customer_group_id'));
        }

        // Filter by Prime tier (customer group) when prime_tier_filter is set
        $prime_tier = request()->input('prime_tier_filter');
        if (! empty($prime_tier)) {
            $groupIds = $this->getPrimeTierCustomerGroupIds($business_id, $prime_tier);
            if (! empty($groupIds)) {
                $query->whereIn('contacts.customer_group_id', $groupIds);
            } else {
                // Selected tier has no matching groups → show no customers
                $query->whereIn('contacts.customer_group_id', [0]);
            }
        }

        if (! empty(request()->input('contact_status'))) {
            $query->where('contacts.contact_status', request()->input('contact_status'));
        }

        // Filter by brand_id (location_id is already handled in getContactQuery via $location_id parameter)
        if (! empty(request()->input('brand_id'))) {
            $query->where('contacts.brand_id', request()->input('brand_id'));
        }

        // Add brand relationship for B2C access
        $user = auth()->user();
        $permitted_locations = $user->permitted_locations($business_id);
        $is_admin = $user->can('access_all_locations') || $user->can('admin');
        
        $has_b2c_access = false;
        if ($permitted_locations == 'all') {
            $has_b2c_access = BusinessLocation::where('business_id', $business_id)
                ->where('is_b2c', 1)
                ->exists();
        } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
            $has_b2c_access = BusinessLocation::whereIn('id', $permitted_locations)
                ->where('is_b2c', 1)
                ->exists();
        }

        if ($has_b2c_access || $is_admin) {
            $query->with('brand');
        }

        $query->with('invoices');

        $contacts = Datatables::of($query);
        
        // Add brand column conditionally (after customer_group, before address in the table)
        if ($has_b2c_access || $is_admin) {
            $contacts->addColumn('brand', function ($row) {
                return $row->brand ? $row->brand->name : 'N/A';
            });
        }
        
        $contacts->addColumn('row_select', function ($row) {
                return '<input type="checkbox" class="row-select" value="' . $row->id . '">';
            });

        $contacts->addColumn('address', function ($row) {
                $full_address = implode(", ", array_filter([
                    $row->address_line_1,
                    $row->address_line_2,
                    $row->city,
                    $row->state,
                    $row->country,
                    $row->zip_code,
                ]));

                return Str::limit($full_address, 30);
            });
        
        $contacts->addColumn(
                'due',
                '<span class="contact_due" data-orig-value="{{$total_invoice - $invoice_received - $total_ledger_discount}}" data-highlight=true>@format_currency($total_invoice - $invoice_received - $total_ledger_discount)</span>'
            )
            ->addColumn(
                'return_due',
                '<span class="return_due" data-orig-value="{{$total_sell_return - $sell_return_paid}}" data-highlight=false>@format_currency($total_sell_return - $sell_return_paid)</span>'
            );
        
        $contacts->addColumn(
                'action',
                function ($row) {
                    $html = '<div class="btn-group dropdown scroll-safe-dropdown">
                    <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max dropdown-toggle" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __('messages.actions') .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    if(auth()->user()->can('sell.payments')){
                    $html .= '<li><a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$row->id]) . '?type=sell" class="pay_sale_due"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>' . __('lang_v1.receive_payment') . '</a></li>';
                    $return_due = $row->total_sell_return - $row->sell_return_paid;
                    if ($return_due > 0) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$row->id]) . '?type=sell_return" class="pay_purchase_due"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>' . __('lang_v1.pay_sell_return_due') . '</a></li>';
                    }

                    }

                    if (auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own')) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id, 'type=customer']) . '"><i class="fas fa-eye" aria-hidden="true"></i>' . __('messages.view') . '</a></li>';
                    }
                    if (! $row->is_default && auth()->user()->can('customer.update')) {
                        if ($row->isApproved == 0 || $row->isApproved == '') {
                            $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'approve'], [$row->id]) . '" class="approve_contact_button"><i class="fas fa-check-square"></i>' . __('messages.approve') . '</a></li>';
                        }
                        if ($row->isApproved == 1 || $row->isApproved == '') {
                            $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'notApprove'], [$row->id]) . '" class="not_approve_contact_button"><i class="fa fa-ban" aria-hidden="true"></i>' . __('messages.reject') . '</a></li>';
                        }
                    }
                    if (auth()->user()->can('customer.update')) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'edit'], [$row->id]) . '" class="edit_contact_button"><i class="fas fa-edit" aria-hidden="true"></i>' . __('messages.edit') . '</a></li>';
                    }
                    if (! $row->is_default && auth()->user()->can('customer.delete')) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'destroy'], [$row->id]) . '" class="delete_contact_button"><i class="fas fa-trash" aria-hidden="true"></i>' . __('messages.delete') . '</a></li>';
                    }


                    if (auth()->user()->can('customer.update')) {
                        if (!$row->is_default) {
                            $html .= '<li><a href="' . action([\App\Http\Controllers\ContactController::class, 'updateStatus'], [$row->id]) . '"class="update_contact_status"><i class="fas fa-power-off"></i>';

                            if ($row->contact_status == 'active') {
                                $html .= __('messages.deactivate');
                            } else {
                                $html .= __('messages.activate');
                            }
                            $html .= '</a></li>';
                        }
                    }

                    $html .= '<li class="divider"></li>';
                    if (auth()->user()->can('customer.view')) {
                        $html .= '
                                <li>
                                    <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=ledger">
                                        <i class="fas fa-scroll" aria-hidden="true"></i>
                                        ' . __('lang_v1.ledger') . '
                                    </a>
                                </li>';

                        if (in_array($row->type, ['both', 'supplier'])) {
                            $html .= '<li>
                                <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=purchase">
                                    <i class="fas fa-arrow-circle-down" aria-hidden="true"></i>
                                    ' . __('purchase.purchases') . '
                                </a>
                            </li>
                            <li>
                                <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=stock_report">
                                    <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                                    ' . __('report.stock_report') . '
                                </a>
                            </li>';
                        }

                        if (in_array($row->type, ['both', 'customer'])) {
                            $html .= '<li>
                                <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=sales">
                                    <i class="fas fa-arrow-circle-up" aria-hidden="true"></i>
                                    ' . __('sale.sells') . '
                                </a>
                            </li>';
                        }

                        $html .= '<li>
                                <a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '?view=documents_and_notes">
                                    <i class="fas fa-paperclip" aria-hidden="true"></i>
                                     ' . __('lang_v1.documents_and_notes') . '
                                </a>
                            </li>';
                    }
                    $html .= '</ul></div>';

                    return $html;
                }
            )
            ->editColumn('opening_balance', function ($row) {
                $html = '<span data-orig-value="' . $row->opening_balance . '">' . $this->transactionUtil->num_f($row->opening_balance, true) . '</span>';

                return $html;
            })
            ->addColumn('invoices', function ($row) {
                $html = '<span data-orig-value="' . $row->invoices->count() . '">' . $row->invoices->count() . '</span>';
                return $html;
            })
            ->editColumn('balance', function ($row) {
                $html = '<span data-orig-value="' . $row->balance . '">' . $this->transactionUtil->num_f($row->balance, true) . '</span>';

                return $html;
            })
            ->editColumn('credit_limit', function ($row) {
                $html = __('lang_v1.no_limit');
                if (! is_null($row->credit_limit)) {
                    $html = '<span data-orig-value="' . $row->credit_limit . '">' . $this->transactionUtil->num_f($row->credit_limit, true) . '</span>';
                }

                return $html;
            })
            ->editColumn('pay_term', '
                @if(!empty($pay_term_type) && !empty($pay_term_number))
                    {{$pay_term_number}}
                    @lang("lang_v1.".$pay_term_type)
                @endif
            ')
            ->editColumn('name', function ($row) {
                $name = $row->name;
                if ($row->contact_status == 'inactive') {
                    $name = $row->name . ' <small class="label pull-right bg-red no-print">' . __('lang_v1.inactive') . '</small>';
                }

                if (! empty($row->converted_by)) {
                    $name .= '<span class="label bg-info label-round no-print" data-toggle="tooltip" title="Converted from leads"><i class="fas fa-sync-alt"></i></span>';
                }

                return $name;
            })
            ->editColumn('total_rp', '{{$total_rp ?? 0}}')
            ->editColumn('created_at', '{{@format_date($created_at)}}')
            ->removeColumn('total_invoice')
            ->removeColumn('opening_balance_paid')
            ->removeColumn('invoice_received')
            ->removeColumn('state')
            ->removeColumn('country')
            ->removeColumn('city')
            ->removeColumn('type')
            ->removeColumn('id')
            ->removeColumn('is_default')
            ->removeColumn('total_sell_return')
            ->removeColumn('sell_return_paid')
            ->filterColumn('address', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('address_line_1', 'like', "%{$keyword}%")
                        ->orWhere('address_line_2', 'like', "%{$keyword}%")
                        ->orWhere('city', 'like', "%{$keyword}%")
                        ->orWhere('state', 'like', "%{$keyword}%")
                        ->orWhere('country', 'like', "%{$keyword}%")
                        ->orWhere('zip_code', 'like', "%{$keyword}%")
                        ->orWhereRaw("CONCAT(COALESCE(address_line_1, ''), ', ', COALESCE(address_line_2, ''), ', ', COALESCE(city, ''), ', ', COALESCE(state, ''), ', ', COALESCE(country, '') ) like ?", ["%{$keyword}%"]);
                });
            })
            ->filterColumn('invoices', function ($query, $keyword) {
                $query->whereHas('invoices', function ($q) use ($keyword) {
                    $q->where('invoice_no', 'like', "%{$keyword}%")
                        ->orWhere('transaction_date', 'like', "%{$keyword}%")
                        ->orWhere('shipping_address', 'like', "%{$keyword}%")
                        ->orWhere('shipment', 'like', "%{$keyword}%");
                });
            });
        $reward_enabled = (request()->session()->get('business.enable_rp') == 1) ? true : false;
        if (! $reward_enabled) {
            $contacts->removeColumn('total_rp');
        }
        $contacts->setRowAttr([
            'href' => function ($row) {
                if (auth()->user()->can('customer.view')) {
                    return  action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id, 'type=customer']);
                } else {
                    return '';
                }
            },
        ]);

        $rawColumns = ['row_select', 'action', 'invoices', 'opening_balance', 'credit_limit', 'pay_term', 'due', 'return_due', 'name', 'balance'];
        
        return $contacts->rawColumns($rawColumns)
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('supplier.create') && ! auth()->user()->can('customer.create') && ! auth()->user()->can('customer.view_own') && ! auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $selected_type = request()->type;

        $types = [];
        // Filter types based on selected_type - enable all contact types when user has permissions
        if ($selected_type == 'supplier') {
            if (auth()->user()->can('supplier.create') || auth()->user()->can('supplier.view_own')) {
                $types['supplier'] = __('report.supplier');
            }
            if (auth()->user()->can('customer.create') || auth()->user()->can('customer.view_own')) {
                $types['customer'] = __('report.customer');
            }
            if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
                $types['both'] = __('lang_v1.both_supplier_customer');
            }
        } elseif ($selected_type == 'customer') {
            if (auth()->user()->can('supplier.create') || auth()->user()->can('supplier.view_own')) {
                $types['supplier'] = __('report.supplier');
            }
            if (auth()->user()->can('customer.create') || auth()->user()->can('customer.view_own')) {
                $types['customer'] = __('report.customer');
            }
            if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
                $types['both'] = __('lang_v1.both_supplier_customer');
            }
        } else {
            // If no specific type selected, show all types
            if (auth()->user()->can('supplier.create') || auth()->user()->can('supplier.view_own')) {
                $types['supplier'] = __('report.supplier');
            }
            if (auth()->user()->can('customer.create') || auth()->user()->can('customer.view_own')) {
                $types['customer'] = __('report.customer');
            }
            if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
                $types['both'] = __('lang_v1.both_supplier_customer');
            }
        }

        $customer_groups = CustomerGroup::forDropdown($business_id);
        

        $module_form_parts = $this->moduleUtil->getModuleData('contact_form_part');

        //Added check because $users is of no use if enable_contact_assign if false
        $users = config('constants.enable_contact_assign') ? User::forDropdown($business_id, false, false, false, true) : [];
        
        // Get business locations for super admins
        $business_locations = [];
        if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
            $business_locations = BusinessLocation::forDropdown($business_id);
        }
        
        $brands=[];
        $is_b2c = false;
        if(!auth()->user()->can('access_all_locations') || !auth()->user()->can('admin')){
            $location_id = $this->getLocationForContact(request());
            $brands=Brands::forDropdown($business_id, false, false, $location_id);
            $is_b2c = BusinessLocation::where('id', $location_id)->value('is_b2c');
        }

        return view('contact.create')
            ->with(compact('types', 'customer_groups', 'selected_type', 'module_form_parts', 'users', 'business_locations', 'brands', 'is_b2c'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('supplier.create') && ! auth()->user()->can('customer.create') && ! auth()->user()->can('customer.view_own') && ! auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            if (! $this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }

            $input = $request->only([
                'type',
                // 'supplier_business_name',
                'prefix',
                // 'first_name',
                'middle_name',
                'last_name',
                'tax_number',
                'pay_term_number',
                'pay_term_type',
                'mobile',
                'landline',
                'alternate_number',
                // 'city',
                // 'state',
                // 'country',
                // 'address_line_1',
                // 'address_line_2',
                'customer_group_id',
                // 'zip_code',
                'contact_id',
                'custom_field1',
                'custom_field2',
                'custom_field3',
                'custom_field4',
                'custom_field5',
                'custom_field6',
                'custom_field7',
                'custom_field8',
                'custom_field9',
                'custom_field10',
                'email',
                // 'shipping_address',
                'position',
                'dob',
                'shipping_custom_field_details',
                'assigned_to_users',
                'password',
                'customer_u_name',
                'isApproved',
                'is_tax_exempt',
                // Dropshipping vendor fields
                'vendor_type',
                'commission_type',
                'dropship_payment_terms',
                'dropship_payment_method',
                'lead_time_days',
                'min_order_qty',
                'dropship_notes'
            ]);
            
            // Handle checkbox - set to 0 if not present
            $input['is_tax_exempt'] = $request->has('is_tax_exempt') ? 1 : 0;
            
            // Handle dropshipping vendor numeric fields
            $input['commission_value'] = $request->input('commission_value') != '' ? $this->commonUtil->num_uf($request->input('commission_value')) : null;
            $input['default_markup_percentage'] = $request->input('default_markup_percentage') != '' ? $this->commonUtil->num_uf($request->input('default_markup_percentage')) : null;
            $input['margin_percentage'] = $request->input('margin_percentage') != '' ? $this->commonUtil->num_uf($request->input('margin_percentage')) : null;
            $input['auto_forward_orders'] = $request->has('auto_forward_orders') ? 1 : 0;

            $name_array = [];

            if (! empty($request->input('prefix'))) {
                $name_array[] = $request->input('prefix');
            }
            if (! empty($request->input('first_name'))) {
                $name_array[] = $request->input('first_name');
            }
            if (! empty($request->input('middle_name'))) {
                $name_array[] = $request->input('middle_name');
            }
            if (! empty($request->input('last_name'))) {
                $name_array[] = $request->input('last_name');
            }

            $input['contact_type'] = $request->input('contact_type_radio');

            $input['name'] = trim(implode(' ', $name_array));

            if (! empty($request->input('is_export'))) {
                $input['is_export'] = true;
                $input['export_custom_field_1'] = $request->input('export_custom_field_1');
                $input['export_custom_field_2'] = $request->input('export_custom_field_2');
                $input['export_custom_field_3'] = $request->input('export_custom_field_3');
                $input['export_custom_field_4'] = $request->input('export_custom_field_4');
                $input['export_custom_field_5'] = $request->input('export_custom_field_5');
                $input['export_custom_field_6'] = $request->input('export_custom_field_6');
            }
            $addressType = $request->input('address_type');

            switch ($addressType) {
                case 'Shipping':

                    $input['shipping_first_name'] = $request->input('first_name');
                    $input['shipping_last_name'] = $request->input('last_name');
                    $input['shipping_company'] = $request->input('supplier_business_name');
                    $input['shipping_address1'] = $request->input('address_line_1');
                    $input['shipping_address2'] = $request->input('address_line_2');
                    $input['shipping_city'] = $request->input('city');
                    $input['shipping_state'] = $request->input('state');
                    $input['shipping_zip'] = $request->input('zip_code');
                    $input['shipping_country'] = $request->input('country');
                    $input['shipping_address'] = $input['shipping_address1'] . ' ' .
                        ($input['shipping_address2'] ? $input['shipping_address2'] . ' ' : '') .
                        $input['shipping_city'] . ' ' .
                        $input['shipping_state'] . ' ' .
                        $input['shipping_zip'] . ' ' .
                        $input['shipping_country'];
                    break;

                case 'Billing':
                    $input['first_name'] = $request->input('first_name');
                    $input['last_name'] = $request->input('last_name');
                    $input['supplier_business_name'] = $request->input('supplier_business_name');
                    $input['address_line_1'] = $request->input('address_line_1');
                    $input['address_line_2'] = $request->input('address_line_2');
                    $input['city'] = $request->input('city');
                    $input['state'] = $request->input('state');
                    $input['zip_code'] = $request->input('zip_code');
                    $input['country'] = $request->input('country');
                    break;

                case 'Both':
                    $input['first_name'] = $request->input('first_name');
                    $input['last_name'] = $request->input('last_name');
                    $input['supplier_business_name'] = $request->input('supplier_business_name');
                    $input['address_line_1'] = $request->input('address_line_1');
                    $input['address_line_2'] = $request->input('address_line_2');
                    $input['city'] = $request->input('city');
                    $input['state'] = $request->input('state');
                    $input['zip_code'] = $request->input('zip_code');
                    $input['country'] = $request->input('country');

                    $input['shipping_first_name'] = $request->input('first_name');
                    $input['shipping_last_name'] = $request->input('last_name');
                    $input['shipping_company'] = $request->input('supplier_business_name');
                    $input['shipping_address1'] = $request->input('address_line_1');
                    $input['shipping_address2'] = $request->input('address_line_2');
                    $input['shipping_city'] = $request->input('city');
                    $input['shipping_state'] = $request->input('state');
                    $input['shipping_zip'] = $request->input('zip_code');
                    $input['shipping_country'] = $request->input('country');
                    $input['shipping_address'] = $input['shipping_address1'] . ' ' .
                        ($input['shipping_address2'] ? $input['shipping_address2'] . ' ' : '') .
                        $input['shipping_city'] . ' ' .
                        $input['shipping_state'] . ' ' .
                        $input['shipping_zip'] . ' ' .
                        $input['shipping_country'];
                    break;
            }
            if (! empty($input['dob'])) {
                $input['dob'] = $this->commonUtil->uf_date($input['dob']);
            }
            $input['password'] = $request->input('password');
            if (!empty($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            } else {
                unset($input['password']);
            }
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            
            // Auto-detect location for regular users, allow selection for super admins
            $input['location_id'] = $this->getLocationForContact($request);
            $input['brand_id'] = $request->input('brand_id');

            $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
            $input['transaction_limit'] = $request->input('transaction_limit') != '' ? $this->commonUtil->num_uf($request->input('transaction_limit')) : null;
            $input['is_auto_send_due_notification'] = $request->input('is_auto_send_due_notification');
            $input['opening_balance'] = $this->commonUtil->num_uf($request->input('opening_balance'));
            DB::beginTransaction();
            $output = $this->contactUtil->createNewContact($input);

            event(new ContactCreatedOrModified($input, 'added'));

            $this->moduleUtil->getModuleData('after_contact_saved', ['contact' => $output['data'], 'input' => $request->input()]);

            $this->contactUtil->activityLog($output['data'], 'added');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('supplier.view') && ! auth()->user()->can('customer.view') && ! auth()->user()->can('customer.view_own') && ! auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $contact = $this->contactUtil->getContactInfo($business_id, $id);
        $selesRep = Contact::with('haveSelesRep')->where('id', $id)->first();
        $selsRepInfo = $selesRep->haveSelesRep ?? [];
        $customerTier = CustomerGroup::where('id', $selesRep->customer_group_id)->value('name', 'selling_price_group_id') ?? 'N/A';


        $is_selected_contacts = User::isSelectedContacts(auth()->user()->id);
        $user_contacts = [];
        if ($is_selected_contacts) {
            $user_contacts = auth()->user()->contactAccess->pluck('id')->toArray();
        }

        if (! auth()->user()->can('supplier.view') && auth()->user()->can('supplier.view_own')) {
            if ($contact->created_by != auth()->user()->id & ! in_array($contact->id, $user_contacts)) {
                abort(403, 'Unauthorized action.');
            }
        }
        if (! auth()->user()->can('customer.view') && auth()->user()->can('customer.view_own')) {
            if ($contact->created_by != auth()->user()->id & ! in_array($contact->id, $user_contacts)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && in_array($contact->type, ['customer', 'both'])) ? true : false;

        $contact_dropdown = Contact::contactDropdown($business_id, false, false, true, $isViewContact = true);

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        //get contact view type : ledger, notes etc.
        $view_type = request()->get('view');
        if (is_null($view_type)) {
            $view_type = 'ledger';
        }

        $expensesDue = $this->transactionUtil->getTotalExpenses($contact->id) ?? null;
        $expensesPaid = $this->transactionUtil->getTotalExpenses($contact->id, 'paid') ?? null;
        $contact_view_tabs = $this->moduleUtil->getModuleData('get_contact_view_tabs');
        // over all balance due
        $balance_due_header = $this->transactionUtil->getContactBalanceDue($contact->id);
        $activities = Activity::forSubject($contact)
            ->with(['causer', 'subject'])
            ->latest()
            ->get();

        $return_due_header = $this->transactionUtil->getContactReturnDue($contact->id);

        return view('contact.show')
            ->with(compact('contact', 'selsRepInfo', 'customerTier', 'balance_due_header', 'expensesDue', 'expensesPaid', 'reward_enabled', 'contact_dropdown', 'business_locations', 'view_type', 'contact_view_tabs', 'activities','return_due_header'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('supplier.update') && ! auth()->user()->can('customer.update') && ! auth()->user()->can('customer.view_own') && ! auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $contact = Contact::where('business_id', $business_id)->find($id);

            if (! $this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
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

            $ob_transaction = Transaction::where('contact_id', $id)
                ->where('type', 'opening_balance')
                ->first();
            $opening_balance = ! empty($ob_transaction->final_total) ? $ob_transaction->final_total : 0;

            //Deduct paid amount from opening balance.
            if (! empty($opening_balance)) {
                $opening_balance_paid = $this->transactionUtil->getTotalAmountPaid($ob_transaction->id);
                if (! empty($opening_balance_paid)) {
                    $opening_balance = $opening_balance - $opening_balance_paid;
                }

                $opening_balance = $this->commonUtil->num_f($opening_balance);
            }

            //Added check because $users is of no use if enable_contact_assign if false
            $users = config('constants.enable_contact_assign') ? User::forDropdown($business_id, false, true, false, true) : [];
            
            // Get business locations for super admins
            $business_locations = [];
            if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
                $business_locations = BusinessLocation::forDropdown($business_id);
            }
            $brands=[];
            $is_b2c = false;
            if(!auth()->user()->can('access_all_locations') || !auth()->user()->can('admin')){
                $location_id = $this->getLocationForContact(request());
                $brands=Brands::forDropdown($business_id, false, false, $location_id);
                $is_b2c = BusinessLocation::where('id', $contact->location_id)->value('is_b2c');
            }else{
                $is_b2c = BusinessLocation::where('id', $contact->location_id)->value('is_b2c');
                $location_id = $contact->location_id;
                $brands=Brands::forDropdown($business_id, false, false, $location_id);
            }
            // dd($is_b2c);

            return view('contact.edit')
                ->with(compact('contact', 'types', 'customer_groups', 'opening_balance', 'users', 'business_locations', 'brands', 'is_b2c'));
        }
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
        if (! auth()->user()->can('supplier.update') && ! auth()->user()->can('customer.update') && ! auth()->user()->can('customer.view_own') && ! auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only([
                    'type',
                    // 'supplier_business_name',
                    'prefix',
                    // 'first_name',
                    'middle_name',
                    'last_name',
                    'tax_number',
                    'pay_term_number',
                    'pay_term_type',
                    'mobile',
                    // 'address_line_1',
                    // 'address_line_2',
                    // 'zip_code',
                    'dob',
                    'alternate_number',
                    // 'city',
                    // 'state',
                    // 'country',
                    'landline',
                    'customer_group_id',
                    'contact_id',
                    'custom_field1',
                    'custom_field2',
                    'custom_field3',
                    'custom_field4',
                    'custom_field5',
                    'custom_field6',
                    'custom_field7',
                    'custom_field8',
                    'custom_field9',
                    'custom_field10',
                    'email',
                    // 'shipping_address',
                    'position',
                    'shipping_custom_field_details',
                    'export_custom_field_1',
                    'export_custom_field_2',
                    'export_custom_field_3',
                    'export_custom_field_4',
                    'export_custom_field_5',
                    'export_custom_field_6',
                    'assigned_to_users',
                    'password',
                    'customer_u_name',
                    'isApproved',
                    'is_tax_exempt',
                    // Dropshipping vendor fields
                    'vendor_type',
                    'commission_type',
                    'dropship_payment_terms',
                    'dropship_payment_method',
                    'lead_time_days',
                    'min_order_qty',
                    'dropship_notes'
                ]);
                
                // Handle checkbox - set to 0 if not present
                $input['is_tax_exempt'] = $request->has('is_tax_exempt') ? 1 : 0;
                
                // Handle dropshipping vendor numeric fields
                $input['commission_value'] = $request->input('commission_value') != '' ? $this->commonUtil->num_uf($request->input('commission_value')) : null;
                $input['default_markup_percentage'] = $request->input('default_markup_percentage') != '' ? $this->commonUtil->num_uf($request->input('default_markup_percentage')) : null;
                $input['margin_percentage'] = $request->input('margin_percentage') != '' ? $this->commonUtil->num_uf($request->input('margin_percentage')) : null;
                $input['auto_forward_orders'] = $request->has('auto_forward_orders') ? 1 : 0;

                $name_array = [];

               if (! empty($request->input('prefix'))) {
                    $name_array[] = $request->input('prefix');
                }
                if (! empty($request->input('first_name'))) {
                    $name_array[] =$request->input('first_name');
                }
                if (! empty($request->input('middle_name'))) {
                    $name_array[] = $request->input('middle_name');
                }
                if (! empty($request->input('last_name'))) {
                    $name_array[] = $request->input('last_name');
                }

                $input['contact_type'] = $request->input('contact_type_radio');
                $addressType = $request->input('address_type');



                switch ($addressType) {
                    case 'Shipping':

                        $input['shipping_first_name'] = $request->input('first_name');
                        $input['shipping_last_name'] = $request->input('last_name');
                        $input['shipping_company'] = $request->input('supplier_business_name');
                        $input['shipping_address1'] = $request->input('address_line_1');
                        $input['shipping_address2'] = $request->input('address_line_2');
                        $input['shipping_city'] = $request->input('city');
                        $input['shipping_state'] = $request->input('state');
                        $input['shipping_zip'] = $request->input('zip_code');
                        $input['shipping_country'] = $request->input('country');
                        $input['shipping_address'] = $input['shipping_address1'] . ' ' .
                            ($input['shipping_address2'] ? $input['shipping_address2'] . ' ' : '') .
                            $input['shipping_city'] . ' ' .
                            $input['shipping_state'] . ' ' .
                            $input['shipping_zip'] . ' ' .
                            $input['shipping_country'];
                        break;

                    case 'Billing':
                        $input['first_name'] = $request->input('first_name');
                        $input['last_name'] = $request->input('last_name');
                        $input['supplier_business_name'] = $request->input('supplier_business_name');
                        $input['address_line_1'] = $request->input('address_line_1');
                        $input['address_line_2'] = $request->input('address_line_2');
                        $input['city'] = $request->input('city');
                        $input['state'] = $request->input('state');
                        $input['zip_code'] = $request->input('zip_code');
                        $input['country'] = $request->input('country');
                        break;

                    case 'Both':
                        $input['first_name'] = $request->input('first_name');
                        $input['last_name'] = $request->input('last_name');
                        $input['supplier_business_name'] = $request->input('supplier_business_name');
                        $input['address_line_1'] = $request->input('address_line_1');
                        $input['address_line_2'] = $request->input('address_line_2');
                        $input['city'] = $request->input('city');
                        $input['state'] = $request->input('state');
                        $input['zip_code'] = $request->input('zip_code');
                        $input['country'] = $request->input('country');

                        $input['shipping_first_name'] = $request->input('first_name');
                        $input['shipping_last_name'] = $request->input('last_name');
                        $input['shipping_company'] = $request->input('supplier_business_name');
                        $input['shipping_address1'] = $request->input('address_line_1');
                        $input['shipping_address2'] = $request->input('address_line_2');
                        $input['shipping_city'] = $request->input('city');
                        $input['shipping_state'] = $request->input('state');
                        $input['shipping_zip'] = $request->input('zip_code');
                        $input['shipping_country'] = $request->input('country');
                        $input['shipping_address'] = $input['shipping_address1'] . ' ' .
                            ($input['shipping_address2'] ? $input['shipping_address2'] . ' ' : '') .
                            $input['shipping_city'] . ' ' .
                            $input['shipping_state'] . ' ' .
                            $input['shipping_zip'] . ' ' .
                            $input['shipping_country'];
                        break;
                }




                $input['password'] = $request->input('password');
                if (!empty($input['password'])) {
                    $input['password'] = Hash::make($input['password']);
                } else {
                    unset($input['password']);
                }


                $input['name'] = trim(implode(' ', $name_array));

                $input['is_export'] = ! empty($request->input('is_export')) ? 1 : 0;

                if (! $input['is_export']) {
                    unset($input['export_custom_field_1'], $input['export_custom_field_2'], $input['export_custom_field_3'], $input['export_custom_field_4'], $input['export_custom_field_5'], $input['export_custom_field_6']);
                }

                if (! empty($input['dob'])) {
                    $input['dob'] = $this->commonUtil->uf_date($input['dob']);
                }

                $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
                $input['transaction_limit'] = $request->input('transaction_limit') != '' ? $this->commonUtil->num_uf($request->input('transaction_limit')) : null;
                $input['is_auto_send_due_notification'] = $request->input('is_auto_send_due_notification');
                
                // Auto-detect location for regular users, allow selection for super admins
                $input['location_id'] = $this->getLocationForContact($request);
                $business_id = $request->session()->get('user.business_id');
                $input['brand_id'] = $request->input('brand_id');

                $input['opening_balance'] = $this->commonUtil->num_uf($request->input('opening_balance'));

                if (! $this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse();
                }
                
                $is_change_approve=false;
                if (! empty($request->input('is_change_approve'))) {
                    $is_change_approve=$request->input('is_change_approve');
                }

                $output = $this->contactUtil->updateContact($input, $id, $business_id ,$is_change_approve);

                event(new ContactCreatedOrModified($output['data'], 'updated'));

                $this->contactUtil->activityLog($output['data'], 'edited');
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        if (! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $validated = $request->validate([
                    'selected_contacts' => 'required',
                    'status' => 'required|string|in:active,inactive,pending,rejected,guest',
                ]);

                $selected_contacts_string = $validated['selected_contacts'];
                $status = $validated['status'];

                $ids = array_filter(array_map('intval', explode(',', $selected_contacts_string)));
                if (empty($ids)) {
                    return [
                        'success' => false,
                        'msg' => __('lang_v1.no_row_selected'),
                    ];
                }

                $business_id = $request->session()->get('user.business_id');

                DB::beginTransaction();

                $query = Contact::where('business_id', $business_id)
                    ->whereIn('id', $ids)
                    ->whereIn('type', ['customer', 'both']);

                if ($status === 'active' || $status === 'inactive') {
                    $query->update([
                        'contact_status' => $status,
                        'isApproved' => 1,
                        'is_guest' => 0,
                    ]);
                } elseif ($status === 'pending') {
                    $query->update([
                        'isApproved' => null,
                        'is_guest' => 0,
                    ]);
                } elseif ($status === 'rejected') {
                    $query->update([
                        'isApproved' => 0,
                        'is_guest' => 0,
                    ]);
                } elseif ($status === 'guest') {
                    $query->update([
                        'is_guest' => 1,
                    ]);
                }

                // Log out affected contacts by revoking passport tokens
                Contact::where('business_id', $business_id)
                    ->whereIn('id', $ids)
                    ->get()
                    ->each(function ($contact) {
                        if (!empty($contact->tokens)) {
                            $contact->tokens->each(function ($token) {
                                $token->delete();
                            });
                        }
                    });

                DB::commit();

                return [
                    'success' => true,
                    'msg' => __('contact.updated_success'),
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                return [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('supplier.delete') && ! auth()->user()->can('customer.delete') && ! auth()->user()->can('customer.view_own') && ! auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                //Check if any transaction related to this contact exists
                $count = Transaction::where('business_id', $business_id)
                    ->where('contact_id', $id)
                    ->count();
                if ($count == 0) {
                    $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                    if (! $contact->is_default) {
                        $log_properities = [
                            'id' => $contact->id,
                            'name' => $contact->name,
                            'supplier_business_name' => $contact->supplier_business_name,
                        ];
                        $this->contactUtil->activityLog($contact, 'contact_deleted', $log_properities);

                        //Disable login for associated users
                        User::where('crm_contact_id', $contact->id)
                            ->update(['allow_login' => 0]);

                        $contact->delete();

                        event(new ContactCreatedOrModified($contact, 'deleted'));
                    }
                    $output = [
                        'success' => true,
                        'msg' => __('contact.deleted_success'),
                    ];
                } else {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.you_cannot_delete_this_contact'),
                    ];
                }
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Bulk delete selected contacts (only those with no transactions and not default).
     *
     * @param  \Illuminate\Http\Request  $request  expects selected_contacts (comma-separated IDs)
     * @return array
     */
    public function massDestroy(Request $request)
    {
        if (! auth()->user()->can('supplier.delete') && ! auth()->user()->can('customer.delete') && ! auth()->user()->can('customer.view_own') && ! auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->user()->business_id;
            $selected = $request->input('selected_contacts');
            if (empty($selected)) {
                return [
                    'success' => false,
                    'msg' => __('lang_v1.no_row_selected'),
                ];
            }

            $ids = array_filter(array_map('intval', explode(',', $selected)));
            $deleted = 0;
            $skipped = 0;

            foreach ($ids as $id) {
                $count = Transaction::where('business_id', $business_id)
                    ->where('contact_id', $id)
                    ->count();
                if ($count > 0) {
                    $skipped++;
                    continue;
                }

                $contact = Contact::where('business_id', $business_id)->find($id);
                if (! $contact) {
                    continue;
                }
                if ($contact->is_default) {
                    $skipped++;
                    continue;
                }

                $log_properities = [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'supplier_business_name' => $contact->supplier_business_name,
                ];
                $this->contactUtil->activityLog($contact, 'contact_deleted', $log_properities);
                User::where('crm_contact_id', $contact->id)->update(['allow_login' => 0]);
                $contact->delete();
                event(new ContactCreatedOrModified($contact, 'deleted'));
                $deleted++;
            }

            $msg = $deleted > 0
                ? __('contact.deleted_success') . ' (' . $deleted . ' ' . ($deleted === 1 ? 'contact' : 'contacts') . ')'
                : __('lang_v1.no_row_selected');
            if ($skipped > 0) {
                $msg .= '. ' . $skipped . ' skipped (have transactions or are default).';
            }

            return [
                'success' => $deleted > 0,
                'msg' => $msg,
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            return [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }
    }

    /**
     * Retrieves list of customers, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getCustomers()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');
            $isID = request()->input('isID', false);

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            
            // Get location for filtering
            $location_id = $this->getLocationForContact(request());

            if ($isID) {
                $contacts = Contact::where('contacts.id', $term)->where('contacts.business_id', $business_id)
                    ->leftjoin('customer_groups as cg', 'cg.id', '=', 'contacts.customer_group_id')
                    ->active();
                    
                // Filter by location if provided
                if (!empty($location_id)) {
                    $contacts->where('contacts.location_id', $location_id);
                }
            } else {
                $contacts = Contact::where('contacts.business_id', $business_id)
                    ->leftjoin('customer_groups as cg', 'cg.id', '=', 'contacts.customer_group_id')
                    ->active();
                    
                // Filter by location if provided
                if (!empty($location_id)) {
                    $contacts->where('contacts.location_id', $location_id);
                }
                    
                if (! request()->has('all_contact')) {
                    $contacts->onlyCustomers();
                }

                if (! empty($term)) {
                    $contacts->where(function ($query) use ($term) {
                        $query->where('contacts.name', 'like', '%' . $term . '%')
                            ->orWhere('supplier_business_name', 'like', '%' . $term . '%')
                            ->orWhere('mobile', 'like', '%' . $term . '%')
                            ->orWhere('contacts.contact_id', 'like', '%' . $term . '%')
                            ->orWhere('contacts.email', 'like', '%' . $term . '%');
                    });
                }
            }
            $contacts->select(
                'contacts.id',
                // DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', contacts.name, CONCAT(contacts.name,supplier_business_name, ' (', contacts.contact_id, ')')) AS text"),
                DB::raw("IF(
                    contacts.name IS NULL OR contacts.name = '', 
                    IF(contacts.contact_id IS NULL OR contacts.contact_id = '', contacts.supplier_business_name, CONCAT(contacts.supplier_business_name, ' (', contacts.contact_id, ')')),
                    IF(contacts.contact_id IS NULL OR contacts.contact_id = '', contacts.name, CONCAT(contacts.name, ' (', contacts.contact_id, ')'))
                ) AS text"),
                'mobile',
                'email',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'country',
                'zip_code',
                'shipping_address',
                'pay_term_number',
                'pay_term_type',
                'balance',
                'supplier_business_name',
                'cg.amount as discount_percent',
                'cg.price_calculation_type',
                'cg.selling_price_group_id',
                'shipping_custom_field_details',
                'is_export',
                'export_custom_field_1',
                'export_custom_field_2',
                'export_custom_field_3',
                'export_custom_field_4',
                'export_custom_field_5',
                'export_custom_field_6',
                'export_custom_field_6',
                'custom_field1',
                'shipping_first_name',
                'shipping_last_name',
                'shipping_company',
                'shipping_address1',
                'shipping_address2',
                'shipping_city',
                'shipping_state',
                'shipping_zip',
                'shipping_country',
                'location_id',
                'is_tax_exempt'
            );

            if (request()->session()->get('business.enable_rp') == 1) {
                $contacts->addSelect('total_rp');
            }
            $contacts = $contacts->get();

            return json_encode($contacts);
        }
    }
    public function getInoiceCustomers()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $contacts = Contact::where('contacts.business_id', $business_id)
                ->leftjoin('customer_groups as cg', 'cg.id', '=', 'contacts.customer_group_id')
                ->active();

            if (! request()->has('all_contact')) {
                $contacts->onlyCustomers();
            }

            if (! empty($term)) {
                $contacts->where(function ($query) use ($term) {
                    $query
                        // ->where('contacts.name', 'like', '%'.$term.'%')
                        // ->orWhere('supplier_business_name', 'like', '%'.$term.'%')
                        // ->orWhere('mobile', 'like', '%'.$term.'%')
                        ->where('contacts.contact_id', $term);
                });
            }

            $contacts->select(
                'contacts.id',
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', contacts.name, CONCAT(contacts.name, ' (', contacts.contact_id, ')')) AS text"),
                'mobile',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'country',
                'zip_code',
                'shipping_address',
                'pay_term_number',
                'pay_term_type',
                'balance',
                'supplier_business_name',
                'cg.amount as discount_percent',
                'cg.price_calculation_type',
                'cg.selling_price_group_id',
                'shipping_custom_field_details',
                'is_export',
                'export_custom_field_1',
                'export_custom_field_2',
                'export_custom_field_3',
                'export_custom_field_4',
                'export_custom_field_5',
                'export_custom_field_6',
                'export_custom_field_6',
                'custom_field1',
                'location_id'
            );

            if (request()->session()->get('business.enable_rp') == 1) {
                $contacts->addSelect('total_rp');
            }
            $contacts = $contacts->get();

            return json_encode($contacts);
        }
    }
    /**
     * Checks if the given contact id already exist for the current business.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkContactId(Request $request)
    {
        $contact_id = $request->input('contact_id');

        $valid = 'true';
        if (! empty($contact_id)) {
            $business_id = $request->session()->get('user.business_id');
            $hidden_id = $request->input('hidden_id');

            $query = Contact::where('business_id', $business_id)
                ->where('contact_id', $contact_id);
            if (! empty($hidden_id)) {
                $query->where('id', '!=', $hidden_id);
            }
            $count = $query->count();
            if ($count > 0) {
                $valid = 'false';
            }
        }
        echo $valid;
        exit;
    }
    public function checkContactUserName(Request $request)
    {
        $contact_id = $request->input('customer_u_name');

        $valid = 'true';
        if (! empty($contact_id)) {
            $business_id = $request->session()->get('user.business_id');
            $hidden_id = $request->input('hidden_id');

            $query = Contact::where('business_id', $business_id)
                ->where('customer_u_name', $contact_id);
            if (! empty($hidden_id)) {
                $query->where('id', '!=', $hidden_id);
            }
            $count = $query->count();
            if ($count > 0) {
                $valid = 'false';
            }
        }
        echo $valid;
        exit;
    }
    /**
     * Shows import option for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getImportContacts()
    {
        if (! auth()->user()->can('supplier.create') && ! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        // if (request()->is('contacts/import')) { // erp disable
        //     return redirect('/contacts?type=customer');
        // }

        $zip_loaded = extension_loaded('zip') ? true : false;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import',
            ];

            return view('contact.import')
                ->with('notification', $output);
        } else {
            $business_id = request()->session()->get('user.business_id');
            $business_locations = BusinessLocation::forDropdown($business_id, false);
            return view('contact.import')->with(compact('business_locations'));
        }
    }

    /**
     * Imports contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postImportContacts(Request $request)
    {
        if (! auth()->user()->can('supplier.create') && ! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->commonUtil->notAllowedInDemo();
            if (! empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('contacts_csv')) {
                $file = $request->file('contacts_csv');
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    //Check if 39 no. of columns exists (added brand_id)
                    if (count($value) != 39) {
                        $is_valid = false;
                        $error_msg = 'Number of columns mismatch. Expected 39 columns, got ' . count($value);
                        break;
                    }

                    $row_no = $key + 1;
                    $contact_array = [];

                    //Check contact type
                    $contact_type = '';
                    $contact_types = [
                        'customer' => 'customer',
                        'supplier' => 'supplier',
                        'both' => 'both',
                    ];
                    if (! empty($value[0])) {
                        $contact_type = strtolower(trim($value[0]));
                        if (array_key_exists($contact_type, $contact_types)) {
                            $contact_array['type'] = $contact_types[$contact_type];
                        } else {
                            $is_valid = false;
                            $error_msg = "Invalid contact type '$contact_type' in row no. $row_no. Valid types are: customer, supplier, both.";
                            break;
                        }
                    } else {
                        $is_valid = false;
                        $error_msg = "Contact type is required in row no. $row_no";
                        break;
                    }

                    $contact_array['prefix'] = $value[1];
                    //Check contact name
                    if (! empty($value[2])) {
                        $contact_array['first_name'] = $value[2];
                    } else {
                        $is_valid = false;
                        $error_msg = "First name is required in row no. $row_no";
                        break;
                    }
                    $contact_array['middle_name'] = $value[3];
                    $contact_array['last_name'] = $value[4];
                    $contact_array['name'] = implode(' ', [$contact_array['prefix'], $contact_array['first_name'], $contact_array['middle_name'], $contact_array['last_name']]);

                    //Check business name
                    if (! empty(trim($value[5]))) {
                        $contact_array['supplier_business_name'] = $value[5];
                    }

                    //Check supplier fields
                    if (in_array($contact_type, ['supplier', 'both'])) {
                        //Check pay term (optional)
                        if (trim($value[9]) != '') {
                            $contact_array['pay_term_number'] = trim($value[9]);
                        }

                        //Check pay period (optional, only set if pay_term is provided)
                        if (!empty($contact_array['pay_term_number'])) {
                            $pay_term_type = strtolower(trim($value[10]));
                            if (in_array($pay_term_type, ['days', 'months'])) {
                                $contact_array['pay_term_type'] = $pay_term_type;
                            }
                        }
                    }

                    //Check contact ID
                    if (! empty(trim($value[6]))) {
                        $count = Contact::where('business_id', $business_id)
                            ->where('contact_id', $value[6])
                            ->count();

                        if ($count == 0) {
                            $contact_array['contact_id'] = $value[6];
                        } else {
                            $is_valid = false;
                            $error_msg = "Contact ID already exists in row no. $row_no";
                            break;
                        }
                    } else if (trim($value[6]) == '0') {
                        // auto generate contact id
                        $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts', $business_id);
                        $contact_array['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count, $business_id);
                    }

                    //Tax number
                    if (! empty(trim($value[7]))) {
                        $contact_array['tax_number'] = $value[7];
                    }

                    //Check opening balance
                    if (! empty(trim($value[8])) && $value[8] != 0) {
                        $contact_array['opening_balance'] = trim($value[8]);
                    }

                    //Check credit limit
                    if (trim($value[11]) != '' && in_array($contact_type, ['customer', 'both'])) {
                        $contact_array['credit_limit'] = trim($value[11]);
                    }

                    //Check email (no validation - accept any value)
                    if (! empty(trim($value[12]))) {
                        $contact_array['email'] = trim($value[12]);
                    }

                    //Mobile number
                    if (! empty(trim($value[13]))) {
                        $contact_array['mobile'] = $value[13];
                    } else {
                        $is_valid = false;
                        $error_msg = "Mobile number is required in row no. $row_no";
                        break;
                    }

                    //Alt contact number
                    $contact_array['alternate_number'] = $value[14];

                    //Landline
                    $contact_array['landline'] = $value[15];

                    //City
                    $contact_array['city'] = $value[16];

                    //State
                    $contact_array['state'] = $value[17];

                    //Country
                    $contact_array['country'] = $value[18];

                    //address_line_1
                    $contact_array['address_line_1'] = $value[19];
                    //address_line_2
                    $contact_array['address_line_2'] = $value[20];
                    $contact_array['zip_code'] = $value[21];
                    $contact_array['dob'] = $value[22];

                    // erp fields 
                    $contact_array['password'] = $value[23];
                    $contact_array['isApproved'] = $value[24];
                    $contact_array['customer_u_name'] = $value[25];
                    $contact_array['shipping_first_name'] = $value[26];
                    $contact_array['shipping_last_name'] = $value[27];
                    $contact_array['shipping_company'] = $value[28];
                    $contact_array['shipping_address1'] = $value[29];
                    $contact_array['shipping_address2'] = $value[30];
                    $contact_array['shipping_city'] = $value[31];
                    $contact_array['shipping_state'] = $value[32];
                    $contact_array['shipping_zip'] = $value[33];
                    $contact_array['shipping_country'] = $value[34];
                    $contact_array['shipping_address'] = ($value[29] ? $value[29] . ' ' : '')  . ' ' .
                        ($value[30] ? $value[30] . ' ' : '') .
                        ($value[31] ? $value[31] . ' ' : '') .
                        ($value[32] ? $value[32] . ' ' : '') .
                        ($value[33] ? $value[33] . ' ' : '') .
                        ($value[34] ? $value[34] : '');
                    $contact_array['contact_status'] = $value[35];
                    $contact_array['customer_group_id'] = $value[36]; // if in wordpress role is wholesale_customer ->1 if role is mm_
                    
                    // Location ID (1 = B2B, 2 = B2C) - use form value if not in file
                    $contact_array['location_id'] = !empty($value[37]) ? $value[37] : ($request->input('location_id') ? $request->input('location_id') : null);
                    
                    // Brand ID - use form value if not in file
                    $contact_array['brand_id'] = !empty($value[38]) ? $value[38] : ($request->input('brand_id') ? $request->input('brand_id') : null);
                    
                    $formated_data[] = $contact_array;
                }
                if (! $is_valid) {
                    throw new \Exception($error_msg);
                }

                if (! empty($formated_data)) {
                    foreach ($formated_data as $contact_data) {
                        $ref_count = $this->transactionUtil->setAndGetReferenceCount('contacts');
                        //Set contact id if empty
                        if (empty($contact_data['contact_id'])) {
                            $contact_data['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count);
                        }

                        $opening_balance = 0;
                        if (isset($contact_data['opening_balance'])) {
                            $opening_balance = $contact_data['opening_balance'];
                            unset($contact_data['opening_balance']);
                        }

                        $contact_data['business_id'] = $business_id;
                        $contact_data['created_by'] = $user_id;

                        $contact = Contact::create($contact_data);

                        if (! empty($opening_balance)) {
                            $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $opening_balance, $user_id, false);
                        }

                        $this->transactionUtil->activityLog($contact, 'imported');
                    }
                }

                $output = [
                    'success' => 1,
                    'msg' => __('product.file_imported_successfully'),
                ];

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage(),
            ];

            return redirect()->route('contacts.import')->with('notification', $output);
        }
        $type = ! empty($contact->type) && $contact->type != 'both' ? $contact->type : 'supplier';

        return redirect()->action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => $type])->with('status', $output);
    }

    /**
     * Export contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function exportContacts(Request $request)
    {
        if (! auth()->user()->can('supplier.view') && ! auth()->user()->can('customer.view')) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $contact_type = $request->input('contact_type', 'all'); // 'customer', 'supplier', or 'all'

            $filename = 'contacts-export-' . ($contact_type != 'all' ? $contact_type . '-' : '') . \Carbon::now()->format('Y-m-d') . '.xlsx';

            return Excel::download(
                new ContactsExport($location_id, $business_id, $contact_type),
                $filename,
                \Maatwebsite\Excel\Excel::XLSX
            );
        } catch (\Throwable $e) {
            Log::error('Export contacts failed: ' . $e->getMessage(), ['exception' => $e]);
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Export failed. Please try again or contact support.',
                ], 500);
            }
            return redirect()->back()->with('error', 'Export failed. Please try again or contact support.');
        }
    }

    /**
     * Shows ledger for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getLedger()
    {
        if (! auth()->user()->can('supplier.view') && ! auth()->user()->can('customer.view') && ! auth()->user()->can('supplier.view_own') && ! auth()->user()->can('customer.view_own')) {
            abort(403, 'Unauthorized action.');
        }


        $business_id = request()->session()->get('user.business_id');

        $contact_id = request()->input('contact_id');

        $is_admin = $this->contactUtil->is_admin(auth()->user());

        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $format = request()->format;
        $location_id = request()->location_id;

        $contact = Contact::find($contact_id);
        $is_selected_contacts = User::isSelectedContacts(auth()->user()->id);
        $user_contacts = [];
        if ($is_selected_contacts) {
            $user_contacts = auth()->user()->contactAccess->pluck('id')->toArray();
        }


        $selesRep = Contact::with('haveSelesRep')->where('id', $contact_id)->first();
        $selsRepInfo = $selesRep->haveSelesRep ?? [];
        $last_payment = TransactionPayment::where('payment_for', $contact_id)->orderByDesc('updated_at')->first();
        if (! auth()->user()->can('supplier.view') && auth()->user()->can('supplier.view_own')) {
            if ($contact->created_by != auth()->user()->id & ! in_array($contact->id, $user_contacts)) {
                abort(403, 'Unauthorized action.');
            }
        }
        if (! auth()->user()->can('customer.view') && auth()->user()->can('customer.view_own')) {
            if ($contact->created_by != auth()->user()->id & ! in_array($contact->id, $user_contacts)) {
                abort(403, 'Unauthorized action.');
            }
        }
        $line_details = $format == 'format_3' ? true : false;
        $formatWas = '';
        if ($format == 'format_4') {
            $formatWas = 'format_4';
            $format = 'format_1';
        }
        $ledger_details = $this->transactionUtil->getLedgerDetails($contact_id, $start_date, $end_date, $format, $location_id, $line_details);
        if ($formatWas == 'format_4') {
            $format = 'format_4';
        }
        $location = null;
        if (! empty($location_id)) {
            $location = BusinessLocation::where('business_id', $business_id)->find($location_id);
        }
        if (request()->input('action') == 'pdf') {
            $output_file_name = 'Ledger-' . str_replace(' ', '-', $contact->name) . '-' . $start_date . '-' . $end_date . '.pdf';
            $for_pdf = true;
            if ($format == 'format_2') {
                $html = view('contact.ledger_format_2')
                    ->with(compact('ledger_details', 'contact', 'for_pdf', 'location'))->render();
            } elseif ($format == 'format_3') {
                $html = view('contact.ledger_format_3')
                    ->with(compact('ledger_details', 'contact', 'location', 'is_admin', 'for_pdf'))->render();
            } elseif ($format == 'format_1') {
                $html = view('contact.ledger')
                    ->with(compact('ledger_details', 'contact', 'location', 'is_admin', 'for_pdf'))->render();
            } elseif ($format == 'format_4') {
                $html = view('contact.ledger')
                    ->with(compact('ledger_details', 'contact', 'location', 'is_admin', 'for_pdf'))->render();
            }

            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output($output_file_name, 'I');
        }
        // return response()->json([$contact]);
        if ($format == 'format_2') {
            return view('contact.ledger_format_2')
                ->with(compact('ledger_details', 'contact', 'location'));
        } elseif ($format == 'format_3') {
            return view('contact.ledger_format_3')
                ->with(compact('ledger_details', 'contact', 'location', 'is_admin'));
        } else if ($format == 'format_4') {
            return view('contact.ledger_format_4')
                ->with(compact('ledger_details', 'contact', 'location', 'is_admin', 'selsRepInfo', 'last_payment'));
        } elseif ($format == 'format_1') {
            return view('contact.ledger')
                ->with(compact('ledger_details', 'contact', 'location', 'is_admin'));
        }
    }

    public function postCustomersApi(Request $request)
    {
        try {
            $api_token = $request->header('API-TOKEN');

            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $business = Business::find($api_settings->business_id);

            $data = $request->only(['name', 'email']);

            $customer = Contact::where('business_id', $api_settings->business_id)
                ->where('email', $data['email'])
                ->whereIn('type', ['customer', 'both'])
                ->first();

            if (empty($customer)) {
                $data['type'] = 'customer';
                $data['business_id'] = $api_settings->business_id;
                $data['created_by'] = $business->owner_id;
                $data['mobile'] = 0;

                $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts', $business->id);

                $data['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count, $business->id);

                $customer = Contact::create($data);
            }
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($customer);
    }

    /**
     * Function to send ledger notification
     */
    public function sendLedger(Request $request)
    {
        $notAllowed = $this->notificationUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $data = $request->only(['to_email', 'subject', 'email_body', 'cc', 'bcc', 'ledger_format']);
            $emails_array = array_map('trim', explode(',', $data['to_email']));

            $contact_id = $request->input('contact_id');
            $business_id = request()->session()->get('business.id');

            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $location_id = request()->input('location_id');

            $contact = Contact::find($contact_id);

            $ledger_details = $this->transactionUtil->getLedgerDetails($contact_id, $start_date, $end_date, $data['ledger_format'], $location_id);

            $orig_data = [
                'email_body' => $data['email_body'],
                'subject' => $data['subject'],
            ];

            $tag_replaced_data = $this->notificationUtil->replaceTags($business_id, $orig_data, null, $contact);
            $data['email_body'] = $tag_replaced_data['email_body'];
            $data['subject'] = $tag_replaced_data['subject'];

            //replace balance_due
            $data['email_body'] = str_replace('{balance_due}', $this->notificationUtil->num_f($ledger_details['balance_due']), $data['email_body']);

            $data['email_settings'] = request()->session()->get('business.email_settings');

            $for_pdf = true;
            if ($data['ledger_format'] == 'format_2') {
                $html = view('contact.ledger_format_2')
                    ->with(compact('ledger_details', 'contact', 'for_pdf'))->render();
            } else {
                $html = view('contact.ledger')
                    ->with(compact('ledger_details', 'contact', 'for_pdf'))->render();
            }

            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);

            $path = config('constants.mpdf_temp_path');
            if (! file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file = $path . '/' . time() . '_ledger.pdf';
            $mpdf->Output($file, 'F');

            $data['attachment'] = $file;
            $data['attachment_name'] = 'ledger.pdf';
            \Notification::route('mail', $emails_array)
                ->notify(new CustomerNotification($data));

            if (file_exists($file)) {
                unlink($file);
            }

            $output = ['success' => 1, 'msg' => __('lang_v1.notification_sent_successfully')];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => 'File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage(),
            ];
        }

        return $output;
    }

    /**
     * Function to get product stock details for a supplier
     */
    public function getSupplierStockReport($supplier_id)
    {
        //TODO: current stock not calculating stock transferred from other location
        $pl_query_string = $this->commonUtil->get_pl_quantity_sum_string();
        $query = PurchaseLine::join('transactions as t', 't.id', '=', 'purchase_lines.transaction_id')
            ->join('products as p', 'p.id', '=', 'purchase_lines.product_id')
            ->join('variations as v', 'v.id', '=', 'purchase_lines.variation_id')
            ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
            ->join('units as u', 'p.unit_id', '=', 'u.id')
            ->whereIn('t.type', ['purchase', 'purchase_return'])
            ->where('t.contact_id', $supplier_id)
            ->select(
                'p.name as product_name',
                'v.name as variation_name',
                'pv.name as product_variation_name',
                'p.type as product_type',
                'u.short_name as product_unit',
                'v.sub_sku',
                DB::raw('SUM(quantity) as purchase_quantity'),
                DB::raw('SUM(quantity_returned) as total_quantity_returned'),
                DB::raw("SUM((SELECT SUM(TSL.quantity - TSL.quantity_returned) FROM transaction_sell_lines_purchase_lines as TSLPL 
                              JOIN transaction_sell_lines AS TSL ON TSLPL.sell_line_id=TSL.id
                              JOIN transactions AS sell ON sell.id=TSL.transaction_id
                              WHERE sell.status='final' AND sell.type='sell'
                              AND TSLPL.purchase_line_id=purchase_lines.id)) as total_quantity_sold"),
                DB::raw("SUM((SELECT SUM(TSL.quantity - TSL.quantity_returned) FROM transaction_sell_lines_purchase_lines as TSLPL 
                              JOIN transaction_sell_lines AS TSL ON TSLPL.sell_line_id=TSL.id
                              JOIN transactions AS sell ON sell.id=TSL.transaction_id
                              WHERE sell.status='final' AND sell.type='sell_transfer'
                              AND TSLPL.purchase_line_id=purchase_lines.id)) as total_quantity_transfered"),
                DB::raw("SUM( COALESCE(quantity - ($pl_query_string), 0) * purchase_price_inc_tax) as stock_price"),
                DB::raw("SUM( COALESCE(quantity - ($pl_query_string), 0)) as current_stock")
            )->groupBy('purchase_lines.variation_id');

        if (! empty(request()->location_id)) {
            $query->where('t.location_id', request()->location_id);
        }

        $product_stocks = Datatables::of($query)
            ->editColumn('product_name', function ($row) {
                $name = $row->product_name;
                if ($row->product_type == 'variable') {
                    $name .= ' - ' . $row->product_variation_name . '-' . $row->variation_name;
                }

                return $name . ' (' . $row->sub_sku . ')';
            })
            ->editColumn('purchase_quantity', function ($row) {
                $purchase_quantity = 0;
                if ($row->purchase_quantity) {
                    $purchase_quantity = (float) $row->purchase_quantity;
                }

                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $purchase_quantity . '" data-unit="' . $row->product_unit . '" >' . $purchase_quantity . '</span> ' . $row->product_unit;
            })
            ->editColumn('total_quantity_sold', function ($row) {
                $total_quantity_sold = 0;
                if ($row->total_quantity_sold) {
                    $total_quantity_sold = (float) $row->total_quantity_sold;
                }

                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $total_quantity_sold . '" data-unit="' . $row->product_unit . '" >' . $total_quantity_sold . '</span> ' . $row->product_unit;
            })
            ->editColumn('total_quantity_transfered', function ($row) {
                $total_quantity_transfered = 0;
                if ($row->total_quantity_transfered) {
                    $total_quantity_transfered = (float) $row->total_quantity_transfered;
                }

                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $total_quantity_transfered . '" data-unit="' . $row->product_unit . '" >' . $total_quantity_transfered . '</span> ' . $row->product_unit;
            })
            ->editColumn('stock_price', function ($row) {
                $stock_price = 0;
                if ($row->stock_price) {
                    $stock_price = (float) $row->stock_price;
                }

                return '<span class="display_currency" data-currency_symbol=true >' . $stock_price . '</span> ';
            })
            ->editColumn('current_stock', function ($row) {
                $current_stock = 0;
                if ($row->current_stock) {
                    $current_stock = (float) $row->current_stock;
                }

                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $current_stock . '" data-unit="' . $row->product_unit . '" >' . $current_stock . '</span> ' . $row->product_unit;
            });

        return $product_stocks->rawColumns(['current_stock', 'stock_price', 'total_quantity_sold', 'purchase_quantity', 'total_quantity_transfered'])->make(true);
    }

    public function updateStatus($id)
    {
        if (! auth()->user()->can('supplier.update') && ! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $contact = Contact::where('business_id', $business_id)->find($id);
            $contact->contact_status = $contact->contact_status == 'active' ? 'inactive' : 'active';
            $contact->save();
            $contact->tokens->each(function ($token) {
                $token->delete();
            });
            $output = [
                'success' => true,
                'msg' => __('contact.updated_success'),
            ];

            return $output;
        }
    }

    /**
     * Display contact locations on map
     */
    public function contactMap()
    {
        if (! auth()->user()->can('supplier.view') && ! auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $query = Contact::where('business_id', $business_id)
            ->active()
            ->whereNotNull('position');

        if (! empty(request()->input('contacts'))) {
            $query->whereIn('id', request()->input('contacts'));
        }
        $contacts = $query->get();

        $all_contacts = Contact::where('business_id', $business_id)
            ->active()
            ->get();

        return view('contact.contact_map')
            ->with(compact('contacts', 'all_contacts'));
    }

    public function getContactPayments($contact_id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $payments = TransactionPayment::leftjoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                ->leftjoin('transaction_payments as parent_payment', 'transaction_payments.parent_id', '=', 'parent_payment.id')
                ->where('transaction_payments.business_id', $business_id)
                ->whereNull('transaction_payments.parent_id')
                ->with(['child_payments', 'child_payments.transaction'])
                ->where('transaction_payments.payment_for', $contact_id)
                ->select(
                    'transaction_payments.id',
                    'transaction_payments.amount',
                    'transaction_payments.is_return',
                    'transaction_payments.method',
                    'transaction_payments.paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.parent_id',
                    'transaction_payments.transaction_no',
                    't.invoice_no',
                    't.ref_no',
                    't.type as transaction_type',
                    't.return_parent_id',
                    't.id as transaction_id',
                    'transaction_payments.cheque_number',
                    'transaction_payments.card_transaction_number',
                    'transaction_payments.bank_account_number',
                    'transaction_payments.id as DT_RowId',
                    'parent_payment.payment_ref_no as parent_payment_ref_no'
                )
                ->groupBy('transaction_payments.id')
                ->orderByDesc('transaction_payments.paid_on')
                ->paginate();
            $last_payment = TransactionPayment::where('payment_for', $contact_id)->orderByDesc('updated_at')->first();


            // return response()->json([$last_payment]);
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            
            $contact = Contact::find($contact_id);
            $contact_type = $contact ? $contact->type : null;

            return view('contact.partials.contact_payments_tab')
                ->with(compact('payments', 'payment_types', 'last_payment', 'contact_id', 'contact_type'));
        }
    }

    public function getContactDue($contact_id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $due = $this->transactionUtil->getContactDue($contact_id, $business_id);
            $transaction_limit = Contact::find($contact_id)->transaction_limit;

            $output = [
                'due' => $due != 0 ? $this->transactionUtil->num_f($due, true) : '',
                'transaction_limit' => $transaction_limit != null ? $this->transactionUtil->num_f($transaction_limit, true) : '',
            ];

            return $output;
        }
    }

    public function checkMobile(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        $mobile_number = $request->input('mobile_number');

        $query = Contact::where('business_id', $business_id)
            ->where('mobile', 'like', "%{$mobile_number}");

        if (! empty($request->input('contact_id'))) {
            $query->where('id', '!=', $request->input('contact_id'));
        }

        $contacts = $query->pluck('name')->toArray();

        return [
            'is_mobile_exists' => ! empty($contacts),
            'msg' => __('lang_v1.mobile_already_registered', ['contacts' => implode(', ', $contacts), 'mobile' => $mobile_number]),
        ];
    }
    public function approve($id)
    {
        if (! auth()->user()->can('supplier.update') && ! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $contact = Contact::where('business_id', $business_id)->find($id);
            $contact->isApproved = 1;
            $contact->save();

            // If this is a supplier with a dropshipping vendor, update vendor status to active
            if ($contact->type === 'supplier') {
                $wpVendor = WpVendor::where('contact_id', $id)
                    ->where('business_id', $business_id)
                    ->first();
                
                if ($wpVendor) {
                    $wpVendor->status = WpVendor::STATUS_ACTIVE;
                    $wpVendor->save();
                }
            }

            $output = [
                'success' => true,
                'msg' => __('User Profile Approved'),
                'contact_id'=>$id
            ];

            return $output;
        }
    }
    public function notApprove($id)
    {
        if (! auth()->user()->can('supplier.update') && ! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $contact = Contact::where('business_id', $business_id)->find($id);
            $contact->isApproved = 0;
            $contact->save();

            // If this is a supplier with a dropshipping vendor, update vendor status to pending
            if ($contact->type === 'supplier') {
                $wpVendor = WpVendor::where('contact_id', $id)
                    ->where('business_id', $business_id)
                    ->first();
                
                if ($wpVendor) {
                    $wpVendor->status = WpVendor::STATUS_PENDING;
                    $wpVendor->save();
                }
            }

            $output = [
                'success' => true,
                'msg' => __('User Profile Rejected'),
            ];

            return $output;
        }
    }
    /**
     * Get customer cart
     */
    public function getCustomerCart($contact_id)
    {

        $business_id = request()->session()->get('user.business_id');
        $contact = Contact::where('business_id', $business_id)->find($contact_id);
        $priceGroupId = key($contact->price_tier);
        $cart = Cart::where('user_id', $contact_id)->first();
        $userState = $cart->shipping_state ?? $contact->shipping_state;
        $taxCharges = LocationTaxCharge::where('state_code', $userState)
            ->get();
        $cartItems = CartItem::where('user_id', $contact_id)->get();
        $productIds = $cartItems->pluck('product_id');
        $variationIds = $cartItems->pluck('variation_id');
        $products = Product::with([
            'webcategories',
            'brand',
            'customer_price_recalls' => function ($query) use ($contact_id) {
                $query->where('contact_id', $contact_id)
                    ->where('is_active', 1)
                    ->where('is_deleted', 0)
                    ->with(['updatedBy' => function ($q) {
                        $q->select('id', 'first_name', 'last_name');
                    }]);
            },
            'variations' => function ($query) use ($priceGroupId) {
                $query->select([
                    'variations.id',
                    'variations.name',
                    'variations.product_id',
                    'variations.sub_sku',
                    'variations.var_barcode_no',
                    'variations.var_maxSaleLimit',
                    'variations.product_variation_id',
                    'variations.sell_price_inc_tax',
                    DB::raw('COALESCE(variation_group_prices.price_inc_tax, variations.sell_price_inc_tax) as ad_price'),
                    'variation_location_details.in_stock_qty as qty',
                ])->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                    $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                        ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                })->leftJoin('variation_location_details', function ($join) {
                    $join->on('variations.id', '=', 'variation_location_details.variation_id');
                });
            }
        ])
            ->whereIn('id', $productIds)
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->get();

        $cartData = [];
        $count = 0;
        // $subtotal = 0;
        $cart_total_before_tax = 0;
        $cart_final_total = 0;
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                // $subtotal += $variation?->ad_price;

                $unitPrice = $variation?->ad_price;
                $itemPrice = $variation?->ad_price;

                // Check for customer price recall
                $priceRecall = $product->customer_price_recalls
                    ->where('variation_id', $variation?->id)
                    ->first();

                if ($priceRecall) {
                    $unitPrice = $priceRecall->new_price;
                }

                $cart_total_before_tax += $unitPrice * $cartItem->qty;
                $productLocationID = $product->locationTaxType[0];
                $default_purchase_price = $variation?->default_purchase_price;
                $charges = $taxCharges->where('location_id', $productLocationID)->where('state_code', $userState)->first();
                $tax_amount = 0;
                if ($charges) {
                    $taxType = $charges->tax_type;
                    $value = $charges->value;
                    switch ($taxType) {
                        case 'UNIT_BASIS_ML':
                            if (!empty($product->ml) && $product->ml > 0) {
                                $unitPrice += $product->ml * $value;
                                $tax_amount = $product->ml * $value;
                            }
                            break;
                        case 'FLAT_RATE':
                            $unitPrice += $value;
                            $tax_amount = $value;
                            break;
                        case 'PERCENTAGE_ON_SALE':
                            // if($userState == 'IL'){
                            //     $unitPrice += ($value / 100) * $default_purchase_price; // Trump logic 
                            // } else {
                            $unitPrice += ($value / 100) * $unitPrice;
                            $tax_amount = ($value / 100) * $unitPrice;
                            // }
                            break;
                        case 'PERCENTAGE_ON_COST':
                            $unitPrice += ($value / 100) * $default_purchase_price;
                            $tax_amount =  ($value / 100) * $default_purchase_price;
                            break;
                        case 'UNIT_COUNT':
                            if (!empty($product->ct) && $product->ct > 0) {
                                $unitPrice += $product->ct * $value;
                                $tax_amount = $product->ct * $value;
                            }
                            break;
                    }
                }
                $cart_final_total += $unitPrice * $cartItem->qty;
                $cartData[] = [
                    'item_id' => $cartItem->id,
                    'key' => $cartItem->id,
                    'product_id' => $product->id,
                    'variation_id' => $variation?->id ?? null,
                    'ml' => $product->ml ?? null,
                    'ct' => $product->ct ?? null,
                    'locationTaxType' => $product->locationTaxType ?? null,
                    'maxSaleLimit' => $product->maxSaleLimit ?? null,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'product_price' => $variation?->ad_price ?? 0, // 17
                    'product_tax' => $tax_amount,
                    'product_price_with_tax' => $unitPrice,
                    'recalled_price' => $priceRecall ? $priceRecall->new_price : null, // 15
                    'product_image' => $product->image_url,
                    'variation_name' => $variation?->name ?? null,
                    'stock' => $variation?->qty ?? 0,
                    'sku' => $variation?->sub_sku ?? null,
                    'itemBarcode' => $variation?->var_barcode_no ?? null,
                    'purchaseLimit' => $variation?->var_maxSaleLimit ?? null,
                    'stock_status' => ($variation?->qty ?? 0) > 0 ? 'instock' : 'outofstock',
                    'qty' => $cartItem->qty,
                    'has_price_recall' => $priceRecall ? true : false,
                    'old_recall_price' => $priceRecall ? $priceRecall->last_price : null,
                    'last_recall_updated_by' => $priceRecall && $priceRecall->updatedBy ?
                        $priceRecall->updatedBy->first_name . ' ' . $priceRecall->updatedBy->last_name : null,
                    'last_recall_updated_at' => $priceRecall ? $priceRecall->updated_at : null,
                ];
                $count++;
            }
        }
        $isCartAvailable=false;
        if ($cart) {
            $isCartAvailable=true;
            $billing_address = (object)[
                'business_name' => $cart->billing_company ?? "",
                'prefix' => "",
                'first_name' => $cart->billing_first_name ?? "",
                'middle_name' => "",
                'last_name' => $cart->billing_last_name ?? "",
                'full_name' => ($cart->billing_first_name?? "") . " " . ($cart->billing_last_name ?? ""),
                'mobile' => $cart->billing_phone ?? '',
                'email' => $cart->billing_email ?? '',
                'address1' => $cart->billing_address1 ?? '',
                'address2' => $cart->billing_address2 ?? '',
                'city' => $cart->billing_city ?? '',
                'zip_code' => $cart->billing_zip ?? '',
                'state' => $cart->billing_state	 ?? '',
                'country' => $cart->billing_country ?? '',
                'full_address' => trim(($cart->billing_address1 ?? '') . ' ' . ($cart->billing_address2 ?? '') . ' ' . ($cart->billing_city ?? '') . ' ' . ($cart->billing_zip ?? '') . ' ' . ($cart->billing_state ?? '') . ' ' . ($cart->billing_country ?? ''))
            ];
            $shipping_address = (object)[
                'business_name' => $cart->shipping_company ?? "",
                'prefix' => "",
                'first_name' => $cart->shipping_first_name ?? "",
                'middle_name' => "",
                'last_name' => $cart->shipping_last_name ?? "",
                'full_name' => ($cart->shipping_first_name ?? "") . " " . ($cart->shipping_last_name ?? ""),
                'address1' => $cart->shipping_address1 ?? '',
                'address2' => $cart->shipping_address2 ?? '',
                'city' => $cart->shipping_city ?? '',
                'zip_code' => $cart->shipping_zip ?? '',
                'state' => $cart->shipping_state ?? '',
                'country' => $cart->shipping_country ?? '',
                'full_address' => trim(($cart->shipping_address1 ?? '') . ' ' . ($cart->shipping_address2 ?? '') . ' ' . ($cart->shipping_city ?? '') . ' ' . ($cart->shipping_zip ?? '') . ' ' . ($cart->shipping_state ?? '') . ' ' . ($cart->shipping_country ?? ''))
            ];
        } else {
            $isCartAvailable=false;
            $billing_address = (object)[
                'business_name' => $contact->supplier_business_name ?? "",
                'prefix' => $contact->prefix ?? "",
                'first_name' => $contact->first_name ?? "",
                'middle_name' => $contact->middle_name ?? "",
                'last_name' => $contact->last_name ?? "",
                'full_name' => ($contact->prefix ?? "") . " " . ($contact->first_name ?? "") . " " . ($contact->middle_name ? $contact->middle_name . " " : "") . ($contact->last_name ?? ""),
                'mobile' => $contact->mobile ?? '',
                'email' => $contact->email ?? '',
                'address1' => $contact->address_line_1 ?? '',
                'address2' => $contact->address_line_2 ?? '',
                'city' => $contact->city ?? '',
                'zip_code' => $contact->zip_code ?? '',
                'state' => $contact->state ?? '',
                'country' => $contact->country ?? '',
                'full_address' => trim(($contact->address_line_1 ?? '') . ' ' . ($contact->address_line_2 ?? '') . ' ' . ($contact->city ?? '') . ' ' . ($contact->zip_code ?? '') . ' ' . ($contact->state ?? '') . ' ' . ($contact->country ?? ''))
            ];
            $shipping_address = (object)[
                'business_name' => $contact->shipping_company ?? "",
                'prefix' => "",
                'first_name' => $contact->shipping_first_name ?? "",
                'middle_name' => "",
                'last_name' => $contact->shipping_last_name ?? "",
                'full_name' => ($contact->shipping_first_name ?? "") . " " . ($contact->shipping_last_name ?? ""),
                'address1' => $contact->shipping_address1 ?? '',
                'address2' => $contact->shipping_address2 ?? '',
                'city' => $contact->shipping_city ?? '',
                'zip_code' => $contact->shipping_zip ?? '',
                'state' => $contact->shipping_state ?? '',
                'country' => $contact->shipping_country ?? '',
                'full_address' => trim(($contact->shipping_address1 ?? '') . ' ' . ($contact->shipping_address2 ?? '') . ' ' . ($contact->shipping_city ?? '') . ' ' . ($contact->shipping_zip ?? '') . ' ' . ($contact->shipping_state ?? '') . ' ' . ($contact->shipping_country?? ''))
            ];
        }

        // return $cart;
        return view('contact.partials.customer_cart_tab')->with(compact('cartData', 'contact_id', 'cart_total_before_tax', 'cart_final_total', 'billing_address', 'shipping_address','isCartAvailable'));
    }

    public function syncCustomerCart($contact_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $contact = Contact::where('business_id', $business_id)->find($contact_id);
        $priceGroupId = key($contact->price_tier);
        $cart = Cart::where('user_id', $contact_id)->first();
        $userState = $cart->shipping_state ?? $contact->shipping_state;
        $taxCharges = LocationTaxCharge::where('state_code', $userState)
            ->get();
        $cartItems = CartItem::where('user_id', $contact_id)->get();
        $productIds = $cartItems->pluck('product_id');
        $variationIds = $cartItems->pluck('variation_id');
        $products = Product::with([
            'webcategories',
            'brand',
            'customer_price_recalls' => function ($query) use ($contact_id) {
                $query->where('contact_id', $contact_id)
                    ->where('is_active', 1)
                    ->where('is_deleted', 0)
                    ->with(['updatedBy' => function ($q) {
                        $q->select('id', 'first_name', 'last_name');
                    }]);
            },
            'variations' => function ($query) use ($priceGroupId) {
                $query->select([
                    'variations.id',
                    'variations.name',
                    'variations.product_id',
                    'variations.sub_sku',
                    'variations.var_barcode_no',
                    'variations.var_maxSaleLimit',
                    'variations.product_variation_id',
                    'variations.sell_price_inc_tax',
                    DB::raw('COALESCE(variation_group_prices.price_inc_tax, variations.sell_price_inc_tax) as ad_price'),
                    'variation_location_details.in_stock_qty as qty',
                ])->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                    $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                        ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                })->leftJoin('variation_location_details', function ($join) {
                    $join->on('variations.id', '=', 'variation_location_details.variation_id');
                });
            }
        ])
            ->whereIn('id', $productIds)
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->get();

        $cartData = [];
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                $unitPrice = $variation?->ad_price;
                $priceRecall = $product->customer_price_recalls
                    ->where('variation_id', $variation?->id)
                    ->first();

                if ($priceRecall) {
                    $unitPrice = $priceRecall->new_price;
                }
                $productLocationID = $product->locationTaxType[0];
                $default_purchase_price = $variation?->default_purchase_price;
                $charges = $taxCharges->where('location_id', $productLocationID)->where('state_code', $userState)->first();
                $tax_amount = 0;
                if ($charges) {
                    $taxType = $charges->tax_type;
                    $value = $charges->value;
                    switch ($taxType) {
                        case 'UNIT_BASIS_ML':
                            if (!empty($product->ml) && $product->ml > 0) {
                                $unitPrice += $product->ml * $value;
                                $tax_amount = $product->ml * $value;
                            }
                            break;
                        case 'FLAT_RATE':
                            $unitPrice += $value;
                            $tax_amount = $value;
                            break;
                        case 'PERCENTAGE_ON_SALE':
                            $unitPrice += ($value / 100) * $unitPrice;
                            $tax_amount = ($value / 100) * $unitPrice;
                            break;
                        case 'PERCENTAGE_ON_COST':
                            $unitPrice += ($value / 100) * $default_purchase_price;
                            $tax_amount =  ($value / 100) * $default_purchase_price;
                            break;
                        case 'UNIT_COUNT':
                            if (!empty($product->ct) && $product->ct > 0) {
                                $unitPrice += $product->ct * $value;
                                $tax_amount = $product->ct * $value;
                            }
                            break;
                    }
                }
                $cartData[] = [
                    'item_id' => $cartItem->id,
                    'key' => $cartItem->id,
                    'product_id' => $product->id,
                    'variation_id' => $variation?->id ?? null,
                    'ml' => $product->ml ?? null,
                    'ct' => $product->ct ?? null,
                    'locationTaxType' => $product->locationTaxType ?? null,
                    'maxSaleLimit' => $product->maxSaleLimit ?? null,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'product_price' => $variation?->ad_price ?? 0, // 17
                    'product_tax' => $tax_amount,
                    'product_price_with_tax' => $unitPrice,
                    'recalled_price' => $priceRecall ? $priceRecall->new_price : null, // 15
                    'product_image' => $product->image_url,
                    'variation_name' => $variation?->name ?? null,
                    'stock' => $variation?->qty ?? 0,
                    'sku' => $variation?->sub_sku ?? null,
                    'itemBarcode' => $variation?->var_barcode_no ?? null,
                    'purchaseLimit' => $variation?->var_maxSaleLimit ?? null,
                    'stock_status' => ($variation?->qty ?? 0) > 0 ? 'instock' : 'outofstock',
                    'qty' => $cartItem->qty,
                    'has_price_recall' => $priceRecall ? true : false,
                    'old_recall_price' => $priceRecall ? $priceRecall->last_price : null,
                    'last_recall_updated_by' => $priceRecall && $priceRecall->updatedBy ?
                        $priceRecall->updatedBy->first_name . ' ' . $priceRecall->updatedBy->last_name : null,
                    'last_recall_updated_at' => $priceRecall ? $priceRecall->updated_at : null,
                ];
            }
        }

        // return $cart;
        return response()->json($cartData);
    }
    public function getCustomerPrices($contact_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $contact = Contact::where('business_id', $business_id)->find($contact_id);
        $priceGroupId = key($contact->price_tier);
        $userState = $contact->shipping_state;
        $taxCharges = LocationTaxCharge::where('state_code', $userState)->get();
        $recallist = CustomerPriceRecall::where('contact_id', $contact_id)->where('business_id', $business_id)->get();
        $productIds = CustomerPriceRecall::where('contact_id', $contact_id)->where('business_id', $business_id)->pluck('product_id');
        $products = Product::with([
            'webcategories',
            'brand',
            'customer_price_recalls' => function ($query) use ($contact_id) {
                $query->where('contact_id', $contact_id)
                    ->where('is_active', 1)
                    ->where('is_deleted', 0)
                    ->with(['updatedBy' => function ($q) {
                        $q->select('id', 'first_name', 'last_name');
                    }])
                    ->with(['createdBy' => function ($q) {
                        $q->select('id', 'first_name', 'last_name');
                    }]);
            },
            'variations' => function ($query) use ($priceGroupId) {
                $query->select([
                    'variations.id',
                    'variations.name',
                    'variations.product_id',
                    'variations.sub_sku',
                    'variations.var_barcode_no',
                    'variations.var_maxSaleLimit',
                    'variations.product_variation_id',
                    'variations.sell_price_inc_tax',
                    DB::raw('COALESCE(variation_group_prices.price_inc_tax, variations.sell_price_inc_tax) as ad_price'),
                    'variation_location_details.in_stock_qty as qty',
                ])->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                    $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                        ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                })->leftJoin('variation_location_details', function ($join) {
                    $join->on('variations.id', '=', 'variation_location_details.variation_id');
                });
            }
        ])
            ->whereIn('id', $productIds)
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->get();
        $priceRecalls = [];
        $count = 0;
        // $subtotal = 0;
        $cart_total_before_tax = 0;
        $cart_final_total = 0;
        foreach ($recallist as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                // $subtotal += $variation?->ad_price;

                $unitPrice = $variation?->ad_price;
                $itemPrice = $variation?->ad_price;

                // Check for customer price recall
                $priceRecall = $product->customer_price_recalls
                    ->where('variation_id', $variation?->id)
                    ->first();

                if ($priceRecall) {
                    $unitPrice = $priceRecall->new_price;
                }

                $cart_total_before_tax += $unitPrice * $cartItem->qty;
                $productLocationID = $product->locationTaxType[0];
                $default_purchase_price = $variation?->default_purchase_price;
                $charges = $taxCharges->where('location_id', $productLocationID)->where('state_code', $userState)->first();
                if ($charges) {
                    $taxType = $charges->tax_type;
                    $value = $charges->value;
                    switch ($taxType) {
                        case 'UNIT_BASIS_ML':
                            if (!empty($product->ml) && $product->ml > 0) {
                                $unitPrice += $product->ml * $value;
                            }
                            break;
                        case 'FLAT_RATE':
                            $unitPrice += $value;
                            break;
                        case 'PERCENTAGE_ON_SALE':
                            // if($userState == 'IL'){
                            //     $unitPrice += ($value / 100) * $default_purchase_price; // Trump logic 
                            // } else {
                            $unitPrice += ($value / 100) * $unitPrice;
                            // }
                            break;
                        case 'PERCENTAGE_ON_COST':
                            $unitPrice += ($value / 100) * $default_purchase_price;
                            break;
                        case 'UNIT_COUNT':
                            if (!empty($product->ct) && $product->ct > 0) {
                                $unitPrice += $product->ct * $value;
                            }
                            break;
                    }
                }
                $cart_final_total += $unitPrice * $cartItem->qty;
                $priceRecalls[] = [
                    'key' => $cartItem->id,
                    'product_id' => $product->id,
                    'variation_id' => $variation?->id ?? null,
                    'ml' => $product->ml ?? null,
                    'ct' => $product->ct ?? null,
                    'locationTaxType' => $product->locationTaxType ?? null,
                    'maxSaleLimit' => $product->maxSaleLimit ?? null,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'product_price' => $variation?->ad_price ?? 0, // 17
                    'product_price_with_tax' => $unitPrice,
                    'recalled_price' => $priceRecall ? $priceRecall->new_price : null, // 15
                    'product_image' => $product->image_url,
                    'variation_name' => $variation?->name ?? null,
                    'stock' => $variation?->qty ?? 0,
                    'sku' => $variation?->sub_sku ?? null,
                    'itemBarcode' => $variation?->var_barcode_no ?? null,
                    'purchaseLimit' => $variation?->var_maxSaleLimit ?? null,
                    'stock_status' => ($variation?->qty ?? 0) > 0 ? 'instock' : 'outofstock',
                    'qty' => $cartItem->qty,
                    'has_price_recall' => $priceRecall ? true : false,
                    'old_recall_price' => $priceRecall ? $priceRecall->last_price : null,
                    'recall_createdBy' => $priceRecall && $priceRecall->createdBy ?
                        $priceRecall->createdBy->first_name . ' ' . $priceRecall->createdBy->last_name : null,
                    'recall_createdAt' => $priceRecall ? $priceRecall->created_at : null,
                    'last_recall_updated_by' => $priceRecall && $priceRecall->updatedBy ?
                        $priceRecall->updatedBy->first_name . ' ' . $priceRecall->updatedBy->last_name : null,
                    'last_recall_updated_at' => $priceRecall ? $priceRecall->updated_at : null,
                ];
                $count++;
            }
        }
        // return $cartData;
        return view('contact.modal.customer_prices')->with(compact('priceRecalls', 'contact_id'));
    }
    /**
     * Update recall price
     */
    public function updateRecallPrice(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $contact_id = $request->input('contact_id');
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = request()->session()->get('user.business_id');
            $new_price = $request->input('new_price');
            $user_id = request()->session()->get('user.id');

            $priceRecall = CustomerPriceRecall::where('contact_id', $contact_id)
                ->where('product_id', $product_id)
                ->where('variation_id', $variation_id)
                ->where('business_id', $business_id)
                ->first();

            if ($priceRecall) {
                $priceRecall->last_price = $priceRecall->new_price;
                $priceRecall->new_price = $new_price;
                $priceRecall->updated_by = $user_id;
                $priceRecall->updated_at = now();
                $priceRecall->save();
            } else {
                $priceRecall = new CustomerPriceRecall();
                $priceRecall->contact_id = $contact_id;
                $priceRecall->product_id = $product_id;
                $priceRecall->variation_id = $variation_id;
                $priceRecall->business_id = $business_id;
                $priceRecall->last_price = $new_price;
                $priceRecall->new_price = $new_price;
                $priceRecall->is_active = 1;
                $priceRecall->is_deleted = 0;
                $priceRecall->created_by = $user_id;
                $priceRecall->created_at = now();
                $priceRecall->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Price recall updated successfully',
                'data' => [
                    'last_price' => $priceRecall->last_price,
                    'new_price' => $priceRecall->new_price,
                    'updated_at' => $priceRecall->updated_at->format('Y-m-d H:i:s'),
                    'updated_by' => $priceRecall->updatedBy ? $priceRecall->updatedBy->first_name . ' ' . $priceRecall->updatedBy->last_name : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getProductData($variation_id, $contact_id)
    {
        // $contact_id=5;
        $business_id = request()->session()->get('user.business_id');
        $contact = Contact::where('business_id', $business_id)->find($contact_id);
        $priceGroupId = key($contact->price_tier);
        $variation = Variation::where('id', $variation_id)->first();
        $userState = $contact->shipping_state ?? 'IL';
        $taxCharges = LocationTaxCharge::where('state_code', $userState)
            ->get();
        if (!$variation) {
            return null; // Variation not found
        }
        $product = Variation::with(['media' => function ($q) {
            $q->select('id', 'file_name', 'model_id', 'model_type');
        }, 'product' => function ($row) {
            $row->select('id', 'name', 'image', 'ml', 'ct', 'locationTaxType');
        }, 'product_variation', 'variation_location_details', 'group_prices' => function ($q) use ($priceGroupId) {
            $q->where('price_group_id', $priceGroupId);
        }])->whereHas('product', function ($query) {
            $query->where('business_id', request()->session()->get('user.business_id'));
            // $query->where('id', $id);
        })->where('id', $variation_id)->first();
        $productLocationID = $variation->product->locationTaxType;
        $productLocationID = $productLocationID[0];

        $charges = $taxCharges->where('location_id', $productLocationID)->where('state_code', $userState)->first();

        //Get customer group and change the price accordingly
        $customer_id = $contact_id;
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

        $unitPrice = $recall_price ?? $variation->group_prices->where('price_group_id', $priceGroupId)->value('price_inc_tax') ?? $variation->sell_price_inc_tax;
        $tax_amount = 0;
        $default_purchase_price = $variation?->default_purchase_price;

        if ($charges) {
            $taxType = $charges->tax_type;
            $value = $charges->value;
            switch ($taxType) {
                case 'UNIT_BASIS_ML':
                    if (!empty($variation->product->ml) && $variation->product->ml > 0) {
                        $tax_amount = $variation->product->ml * $value;
                        $unitPrice += $tax_amount;
                    }
                    break;
                case 'FLAT_RATE':
                    $unitPrice += $value;
                    break;
                case 'PERCENTAGE_ON_SALE':
                    $tax_amount = ($value / 100) * $unitPrice;
                    $unitPrice += $tax_amount;
                    break;
                case 'PERCENTAGE_ON_COST':
                    $tax_amount = ($value / 100) * $default_purchase_price;
                    $unitPrice += $tax_amount;
                    break;
                case 'UNIT_COUNT':
                    if (!empty($variation->product->ct) && $variation->product->ct > 0) {
                        $tax_amount = $variation->product->ct * $value;
                        $unitPrice += $tax_amount;
                    }
                    break;
            }
        }
        $product['price_with_tax'] = $unitPrice;
        $product['recalled_price'] = $recall_price;
        $product['tax_amount'] = $tax_amount;
        return response()->json($product);
    }

    public function deleteRecallPrice(Request $request)
    {
        $ids = $request->input('ids');
        $uid = $request->session()->get('user.id');

        CustomerPriceRecall::whereIn('id', $ids)->update(['deleted_by' => $uid]);
        CustomerPriceRecall::whereIn('id', $ids)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Price recall deleted successfully'
        ]);
    }

    /**
     * Get customers list for merge dropdown (excluding source contact)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCustomersForMerge(Request $request)
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id')) && !auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $source_contact_id = $request->input('source_contact_id');

        $customers = Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->where('id', '!=', $source_contact_id)
            ->where('contact_status', 'active')
            ->select('id', 'name', 'contact_id', 'supplier_business_name')
            ->get()
            ->map(function ($contact) {
                $display_name = $contact->name;
                if (!empty($contact->supplier_business_name)) {
                    $display_name = $contact->supplier_business_name . ' - ' . $display_name;
                }
                if (!empty($contact->contact_id)) {
                    $display_name .= ' (' . $contact->contact_id . ')';
                }
                return [
                    'id' => $contact->id,
                    'text' => $display_name
                ];
            });

        return response()->json($customers);
    }

    /**
     * Get migration preview data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMergePreview(Request $request)
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id')) && !auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $source_contact_id = $request->input('source_contact_id');
        $source_contact = Contact::findOrFail($source_contact_id);

        $summary = [
            'transactions' => Transaction::where('contact_id', $source_contact_id)->count(),
            'transaction_payments' => TransactionPayment::where('payment_for', $source_contact_id)->count(),
            'addresses' => CustomerAddress::where('contact_id', $source_contact_id)->count(),
            'user_access' => UserContactAccess::where('contact_id', $source_contact_id)->count(),
            'documents' => DocumentAndNote::where('notable_type', Contact::class)
                ->where('notable_id', $source_contact_id)->count(),
            'cart_items' => CartItem::whereHas('cart', function($q) use ($source_contact_id) {
                $q->where('user_id', $source_contact_id);
            })->count(),
            'wishlists' => Wishlist::where('user_id', $source_contact_id)->count(),
            'reviews' => \App\Models\CustomerReview::where('contact_id', $source_contact_id)->count(),
            'credit_applications' => \App\Models\CreditApplication::where('contact_id', $source_contact_id)->count(),
            'stock_alerts' => \App\Models\StockAlert::where('contact_id', $source_contact_id)->count(),
            'complaints' => Complaint::where('contact_id', $source_contact_id)->count(),
            'business_identifications' => BusinessIdentification::where('contact_id', $source_contact_id)->count(),
            'price_recalls' => CustomerPriceRecall::where('contact_id', $source_contact_id)->count(),
        ];

        return response()->json($summary);
    }

    /**
     * Merge customer accounts
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mergeCustomerAccounts(Request $request)
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id')) && !auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'source_contact_id' => 'required|exists:contacts,id',
            'target_contact_id' => 'required|exists:contacts,id|different:source_contact_id',
        ]);

        $source_contact_id = $request->input('source_contact_id');
        $target_contact_id = $request->input('target_contact_id');
        $business_id = $request->session()->get('user.business_id');

        $source_contact = Contact::where('business_id', $business_id)->findOrFail($source_contact_id);
        $target_contact = Contact::where('business_id', $business_id)->findOrFail($target_contact_id);

        // Verify both are customers
        if (!in_array($source_contact->type, ['customer', 'both']) || !in_array($target_contact->type, ['customer', 'both'])) {
            return response()->json([
                'success' => false,
                'msg' => 'Both accounts must be customers'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $migration_log = [];
            $detailed_migration_data = []; // Store detailed data for trash_sources

            // Migrate Transactions
            $transactions = Transaction::where('contact_id', $source_contact_id)->get();
            $transactions_count = $transactions->count();
            if ($transactions_count > 0) {
                $transaction_ids = $transactions->pluck('id')->toArray();
                Transaction::where('contact_id', $source_contact_id)->update(['contact_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$transactions_count} transaction(s)";
                $detailed_migration_data['transactions'] = [
                    'count' => $transactions_count,
                    'ids' => $transaction_ids,
                    'details' => $transactions->map(function($t) {
                        return [
                            'id' => $t->id,
                            'invoice_no' => $t->invoice_no,
                            'type' => $t->type,
                            'final_total' => $t->final_total,
                            'transaction_date' => $t->transaction_date
                        ];
                    })->toArray()
                ];
            }

            // Migrate Transaction Payments
            $payments = TransactionPayment::where('payment_for', $source_contact_id)->get();
            $payments_count = $payments->count();
            if ($payments_count > 0) {
                $payment_ids = $payments->pluck('id')->toArray();
                TransactionPayment::where('payment_for', $source_contact_id)->update(['payment_for' => $target_contact_id]);
                $migration_log[] = "Migrated {$payments_count} payment(s)";
                $detailed_migration_data['transaction_payments'] = [
                    'count' => $payments_count,
                    'ids' => $payment_ids,
                    'total_amount' => $payments->sum('amount')
                ];
            }

            // Migrate Customer Addresses
            $addresses = CustomerAddress::where('contact_id', $source_contact_id)->get();
            $addresses_count = $addresses->count();
            if ($addresses_count > 0) {
                $address_ids = $addresses->pluck('id')->toArray();
                CustomerAddress::where('contact_id', $source_contact_id)->update(['contact_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$addresses_count} address(es)";
                $detailed_migration_data['addresses'] = [
                    'count' => $addresses_count,
                    'ids' => $address_ids
                ];
            }

            // Migrate User Contact Access
            $user_access = UserContactAccess::where('contact_id', $source_contact_id)->get();
            $user_access_count = $user_access->count();
            if ($user_access_count > 0) {
                // Get existing user access for target to avoid duplicates
                $existing_target_access = UserContactAccess::where('contact_id', $target_contact_id)
                    ->pluck('user_id')->toArray();
                
                $user_ids = $user_access->pluck('user_id')->toArray();
                UserContactAccess::where('contact_id', $source_contact_id)
                    ->whereNotIn('user_id', $existing_target_access)
                    ->update(['contact_id' => $target_contact_id]);
                
                // Delete duplicates
                UserContactAccess::where('contact_id', $source_contact_id)->delete();
                $migration_log[] = "Migrated {$user_access_count} user access record(s)";
                $detailed_migration_data['user_access'] = [
                    'count' => $user_access_count,
                    'user_ids' => $user_ids
                ];
            }

            // Migrate Documents and Notes
            $documents = DocumentAndNote::where('notable_type', Contact::class)
                ->where('notable_id', $source_contact_id)->get();
            $documents_count = $documents->count();
            if ($documents_count > 0) {
                $document_ids = $documents->pluck('id')->toArray();
                DocumentAndNote::where('notable_type', Contact::class)
                    ->where('notable_id', $source_contact_id)
                    ->update(['notable_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$documents_count} document(s)/note(s)";
                $detailed_migration_data['documents'] = [
                    'count' => $documents_count,
                    'ids' => $document_ids
                ];
            }

            // Migrate Cart Items (via Cart)
            $carts = Cart::where('user_id', $source_contact_id)->get();
            $cart_items_count = CartItem::whereHas('cart', function($q) use ($source_contact_id) {
                $q->where('user_id', $source_contact_id);
            })->count();
            if ($cart_items_count > 0) {
                $cart_ids = $carts->pluck('id')->toArray();
                Cart::where('user_id', $source_contact_id)->update(['user_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$cart_items_count} cart item(s)";
                $detailed_migration_data['carts'] = [
                    'count' => $carts->count(),
                    'cart_ids' => $cart_ids,
                    'cart_items_count' => $cart_items_count
                ];
            }

            // Migrate Wishlists
            $wishlists = Wishlist::where('user_id', $source_contact_id)->get();
            $wishlists_count = $wishlists->count();
            if ($wishlists_count > 0) {
                $wishlist_ids = $wishlists->pluck('id')->toArray();
                Wishlist::where('user_id', $source_contact_id)->update(['user_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$wishlists_count} wishlist item(s)";
                $detailed_migration_data['wishlists'] = [
                    'count' => $wishlists_count,
                    'ids' => $wishlist_ids
                ];
            }

            // Migrate Customer Reviews
            $reviews = \App\Models\CustomerReview::where('contact_id', $source_contact_id)->get();
            $reviews_count = $reviews->count();
            if ($reviews_count > 0) {
                $review_ids = $reviews->pluck('id')->toArray();
                \App\Models\CustomerReview::where('contact_id', $source_contact_id)->update(['contact_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$reviews_count} review(s)";
                $detailed_migration_data['reviews'] = [
                    'count' => $reviews_count,
                    'ids' => $review_ids
                ];
            }

            // Migrate Credit Applications
            $credit_apps = \App\Models\CreditApplication::where('contact_id', $source_contact_id)->get();
            $credit_apps_count = $credit_apps->count();
            if ($credit_apps_count > 0) {
                $credit_app_ids = $credit_apps->pluck('id')->toArray();
                \App\Models\CreditApplication::where('contact_id', $source_contact_id)->update(['contact_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$credit_apps_count} credit application(s)";
                $detailed_migration_data['credit_applications'] = [
                    'count' => $credit_apps_count,
                    'ids' => $credit_app_ids
                ];
            }

            // Migrate Stock Alerts
            $stock_alerts = \App\Models\StockAlert::where('contact_id', $source_contact_id)->get();
            $stock_alerts_count = $stock_alerts->count();
            if ($stock_alerts_count > 0) {
                $stock_alert_ids = $stock_alerts->pluck('id')->toArray();
                \App\Models\StockAlert::where('contact_id', $source_contact_id)->update(['contact_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$stock_alerts_count} stock alert(s)";
                $detailed_migration_data['stock_alerts'] = [
                    'count' => $stock_alerts_count,
                    'ids' => $stock_alert_ids
                ];
            }

            // Migrate Complaints
            $complaints = Complaint::where('contact_id', $source_contact_id)->get();
            $complaints_count = $complaints->count();
            if ($complaints_count > 0) {
                $complaint_ids = $complaints->pluck('id')->toArray();
                Complaint::where('contact_id', $source_contact_id)->update(['contact_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$complaints_count} complaint(s)";
                $detailed_migration_data['complaints'] = [
                    'count' => $complaints_count,
                    'ids' => $complaint_ids
                ];
            }

            // Migrate Business Identifications
            $business_ids = BusinessIdentification::where('contact_id', $source_contact_id)->get();
            $business_ids_count = $business_ids->count();
            if ($business_ids_count > 0) {
                $business_id_records = $business_ids->pluck('id')->toArray();
                BusinessIdentification::where('contact_id', $source_contact_id)->update(['contact_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$business_ids_count} business identification(s)";
                $detailed_migration_data['business_identifications'] = [
                    'count' => $business_ids_count,
                    'ids' => $business_id_records
                ];
            }

            // Migrate Customer Price Recalls
            $price_recalls = CustomerPriceRecall::where('contact_id', $source_contact_id)->get();
            $price_recalls_count = $price_recalls->count();
            if ($price_recalls_count > 0) {
                $price_recall_ids = $price_recalls->pluck('id')->toArray();
                CustomerPriceRecall::where('contact_id', $source_contact_id)->update(['contact_id' => $target_contact_id]);
                $migration_log[] = "Migrated {$price_recalls_count} price recall(s)";
                $detailed_migration_data['price_recalls'] = [
                    'count' => $price_recalls_count,
                    'ids' => $price_recall_ids
                ];
            }

            // Freeze the source account
            $source_contact->contact_status = 'inactive';
            $source_contact->save();

            // Store source contact data before merge for trash_sources
            $source_contact_data = [
                'id' => $source_contact->id,
                'name' => $source_contact->name,
                'contact_id' => $source_contact->contact_id,
                'email' => $source_contact->email,
                'mobile' => $source_contact->mobile,
                'type' => $source_contact->type,
                'supplier_business_name' => $source_contact->supplier_business_name,
                'customer_group_id' => $source_contact->customer_group_id,
                'location_id' => $source_contact->location_id,
                'brand_id' => $source_contact->brand_id,
                'created_at' => $source_contact->created_at,
                'created_by' => $source_contact->created_by,
            ];

            // Create trash_sources record for enterprise-level auditing
            $trash_source = TrashSource::create([
                'model_type' => Contact::class,
                'model_id' => $source_contact_id,
                'model_name' => $source_contact->name . ' (' . $source_contact->contact_id . ')',
                'action_type' => 'merged',
                'target_model_id' => $target_contact_id,
                'target_model_type' => Contact::class,
                'created_by' => auth()->user()->id,
                'business_id' => $business_id,
                'json_data' => [
                    'source_contact' => $source_contact_data,
                    'target_contact' => [
                        'id' => $target_contact->id,
                        'name' => $target_contact->name,
                        'contact_id' => $target_contact->contact_id,
                    ],
                    'migration_details' => $detailed_migration_data,
                    'migration_summary' => $migration_log,
                    'merged_at' => now()->toDateTimeString(),
                ],
                'description' => 'Customer account merged: ' . $source_contact->name . ' (' . $source_contact->contact_id . ') merged into ' . $target_contact->name . ' (' . $target_contact->contact_id . ') by ' . auth()->user()->username,
            ]);

            // Enhanced activity logging for enterprise-level auditing
            $activity_properties = [
                'source_contact_id' => $source_contact_id,
                'source_contact_name' => $source_contact->name,
                'source_contact_id_number' => $source_contact->contact_id,
                'target_contact_id' => $target_contact_id,
                'target_contact_name' => $target_contact->name,
                'target_contact_id_number' => $target_contact->contact_id,
                'migration_summary' => $migration_log,
                'migration_details' => $detailed_migration_data,
                'migrated_by' => auth()->user()->id,
                'migrated_by_name' => auth()->user()->username,
                'migrated_by_email' => auth()->user()->email,
                'trash_source_id' => $trash_source->id,
                'merge_timestamp' => now()->toDateTimeString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            // Log activity on target contact
            $this->commonUtil->activityLog(
                $target_contact,
                'customer_account_merged',
                $source_contact,
                [
                    'update_note' => 'Customer account merged: ' . $source_contact->name . ' (' . $source_contact->contact_id . ') merged into ' . $target_contact->name . ' (' . $target_contact->contact_id . ')',
                    'properties' => $activity_properties
                ],
                true,
                $business_id
            );

            // Also log activity on source contact (before it's frozen)
            $this->commonUtil->activityLog(
                $source_contact,
                'customer_account_merged_source',
                null,
                [
                    'update_note' => 'This account was merged into ' . $target_contact->name . ' (' . $target_contact->contact_id . ') and frozen.',
                    'properties' => $activity_properties
                ],
                true,
                $business_id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Customer accounts merged successfully',
                'migration_log' => $migration_log
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error merging customer accounts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error merging accounts: ' . $e->getMessage()
            ], 500);
        }
    }
}
