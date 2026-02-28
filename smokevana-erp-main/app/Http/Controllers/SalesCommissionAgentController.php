<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\Utils\Util;
use DataTables;
use DB;
use Illuminate\Http\Request;
use App\Utils\ModuleUtil;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesCommissionAgentController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('user.view') && ! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $users = User::where('business_id', $business_id)
                        ->where('is_cmmsn_agnt', 1)
                        ->select(['id',
                            DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                            'email', 'contact_no', 'address', 'cmmsn_percent', 'status']);

            return Datatables::of($users)
                ->addColumn('status', function ($row) {
                    $status = $row->status ?? 'active';
                    $badge_class = $status == 'active' ? 'label-success' : 'label-danger';
                    $status_text = $status == 'active' ? __('Active') : __('Inactive');
                    return '<span class="label ' . $badge_class . '">' . $status_text . '</span>';
                })
                ->addColumn('locations', function ($row) {
                    $permitted_locations = $row->permitted_locations($row->business_id);
                    
                    if (is_array($permitted_locations) && !empty($permitted_locations)) {
                        $location_names = [];
                        $locations = \App\BusinessLocation::where('business_id', $row->business_id)
                                                        ->where('is_b2c', 0)
                                                        ->whereIn('id', $permitted_locations)
                                                        ->get();
                        
                        foreach ($locations as $location) {
                            $location_names[] = $location->name . (!empty($location->location_id) ? ' (' . $location->location_id . ')' : '');
                        }
                        
                        if (count($location_names) <= 2) {
                            return '<span class="label label-primary"><i class="fas fa-map-marker-alt"></i> ' . implode(', ', $location_names) . '</span>';
                        } else {
                            return '<span class="label label-primary"><i class="fas fa-map-marker-alt"></i> ' . count($location_names) . ' Locations</span>';
                        }
                    } else {
                        return '<span class="label label-default">No Access</span>';
                    }
                })
                ->addColumn(
                    'action',
                    '<div class="tw-flex tw-items-center tw-gap-2 tw-whitespace-nowrap">
                        @can("user.view")
                            <a href="{{action(\'App\Http\Controllers\SalesCommissionAgentController@show\', [$id])}}"
                                class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info">
                                <i class="fa fa-eye"></i> @lang("messages.view")
                            </a>
                        @endcan
                        @can("user.update")
                            <button type="button"
                                data-href="{{action(\'App\Http\Controllers\SalesCommissionAgentController@edit\', [$id])}}"
                                data-container=".commission_agent_modal"
                                class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline btn-modal tw-dw-btn-primary">
                                <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")
                            </button>
                        @endcan
                        @can("user.delete")
                            <button type="button"
                                data-href="{{action(\'App\Http\Controllers\SalesCommissionAgentController@destroy\', [$id])}}"
                                class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_commsn_agnt_button">
                                <i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")
                            </button>
                        @endcan
                    </div>'
                )
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'status', 'locations'])
                ->make(true);
        }

        return view('sales_commission_agent.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $locations = \App\BusinessLocation::where('business_id', $business_id)
                                        ->where('is_b2c', 0)
                                        ->Active()
                                        ->get();

        return view('sales_commission_agent.create')
                    ->with(compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'cmmsn_percent' => 'required|numeric|min:0|max:100',
            'max_discount_percent' => 'nullable|numeric|min:0|max:100',
        ];

        // Add login validation if allow_login is checked
        if ($request->has('allow_login') && $request->input('allow_login')) {
            $rules['username'] = 'nullable|string|max:255|unique:users,username';
            $rules['password'] = 'required|string|min:6';
        }

        $request->validate($rules);

        try {
            $input = $request->only([
                'surname', 'first_name', 'last_name', 'email', 'address', 'contact_no', 'cmmsn_percent',
                'max_discount_percent', 'dob', 'gender', 'marital_status', 'blood_group', 'contact_number', 'alt_number', 
                'family_number', 'guardian_name', 'id_proof_name', 'id_proof_number', 
                'permanent_address', 'current_address', 'status', 'allow_login', 'username', 'password', 'commission_type', 'percentage_value'
            ]);
            
            // Handle bank details separately as it's a JSON field
            $bank_details = [];
            if ($request->has('bank_details') && is_array($request->input('bank_details'))) {
                $bank_details = $request->input('bank_details');
            }
            $input['bank_details'] = !empty($bank_details) ? json_encode($bank_details) : null;
            
            $input['cmmsn_percent'] = $this->commonUtil->num_uf($input['cmmsn_percent']);
            if (!empty($input['max_discount_percent'])) {
                $input['max_discount_percent'] = $this->commonUtil->num_uf($input['max_discount_percent']);
            }
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['is_cmmsn_agnt'] = 1;

            // Handle login credentials
            if (empty($input['allow_login'])) {
                $input['username'] = null;
                $input['password'] = null;
                $input['allow_login'] = 0;
            } else {
                $input['allow_login'] = 1;
                // Hash password if provided
                if (!empty($input['password'])) {
                    $input['password'] = \Hash::make($input['password']);
                }
                // Generate username if not provided
                if (empty($input['username'])) {
                    $ref_count = $this->commonUtil->setAndGetReferenceCount('username', $business_id);
                    $input['username'] = $this->commonUtil->generateReferenceNumber('username', $ref_count, $business_id);
                }
            }

            $user = User::create($input);

            // Grant Location permissions
            $this->commonUtil->giveLocationPermissions($user, $request);

            // Handle B2B customer access
            if ($request->has('b2b_customers') && is_array($request->input('b2b_customers'))) {
                $customer_ids = $request->input('b2b_customers');
                // Filter out empty values
                $customer_ids = array_filter($customer_ids, function($id) {
                    return !empty($id);
                });
                
                foreach ($customer_ids as $customer_id) {
                    try {
                        \App\UserContactAccess::create([
                            'user_id' => $user->id,
                            'contact_id' => $customer_id
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error creating UserContactAccess: ' . $e->getMessage());
                        \Log::error('User ID: ' . $user->id . ', Contact ID: ' . $customer_id);
                    }
                }
            }

            $output = ['success' => true,
                'msg' => __('lang_v1.commission_agent_added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return mixed
     */
    public function show($id)
    {
        if (! auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $user = User::where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)
                    ->with(['contactAccess'])
                    ->findOrFail($id);

        $locations = \App\BusinessLocation::where('business_id', $business_id)
                                        ->Active()
                                        ->get();
        $permitted_locations = $user->permitted_locations();

        $view_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.show', 'user' => $user]);

        $users = User::forDropdown($business_id, false , true, false, false, true);

        $activities = Activity::forSubject($user)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();
        
         $total_sells=Transaction::where('commission_agent', $user->id)->where('type', 'sell')->where('status', 'final')->sum('final_total');


        // Calculate additional commission data
        $monthly_commission = Transaction::where('commission_agent', $user->id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('final_total') * $user->percentage_value / 100;

        $current_quarter_commission = Transaction::where('commission_agent', $user->id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('created_at', [
                now()->startOfQuarter(),
                now()->endOfQuarter()
            ])
            ->sum('final_total') * $user->percentage_value / 100;

        $current_year_commission = Transaction::where('commission_agent', $user->id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereYear('created_at', now()->year)
            ->sum('final_total') * $user->percentage_value / 100;

        $final_commission=$total_sells*$user->percentage_value/100;

        $total_paid =Transaction::where('expense_for', $user->id)->where('type', 'commission_payout')->where('status', 'final')->where('payment_status', 'paid')->sum('final_total'); // Calculate from payouts table

        $total_paid_sells=Transaction::where('commission_agent', $user->id)->where('type', 'sell')->where('status', 'final')->where('payment_status', 'paid')->sum('final_total');
        
        $final_commission_payable=($total_paid_sells*$user->percentage_value/100);

        $pending_payout = max(0, $final_commission - $total_paid);

        $transactions = Transaction::where('commission_agent', $user->id)->where('type', 'sell')->where('status', 'final')->with('contact')->get();
        
        // Get commission payouts for this agent
        $commission_payouts = Transaction::where('expense_for', $user->id)
            ->where('type', 'commission_payout')
            ->where('status', 'final')
            ->with('payment_lines')
            ->orderBy('transaction_date', 'desc')
            ->get();
        
        // ===== Visit History & Stats =====
        // Get today's visits
        $today = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $todayVisits = \App\VisitTracking::whereHas('lead', function($q) use ($business_id) {
                $q->where('business_id', $business_id);
            })
            ->where('sales_rep_id', $user->id)
            ->whereBetween('start_time', [$today, $todayEnd])
            ->with(['lead:id,reference_no,store_name,contact_name,contact_phone'])
            ->orderBy('start_time', 'desc')
            ->get();
        
        // Get yesterday's visits
        $yesterday = now()->subDay()->startOfDay();
        $yesterdayEnd = now()->subDay()->endOfDay();
        $yesterdayVisits = \App\VisitTracking::whereHas('lead', function($q) use ($business_id) {
                $q->where('business_id', $business_id);
            })
            ->where('sales_rep_id', $user->id)
            ->whereBetween('start_time', [$yesterday, $yesterdayEnd])
            ->with(['lead:id,reference_no,store_name,contact_name,contact_phone'])
            ->orderBy('start_time', 'desc')
            ->get();
        
        // Get this week's stats
        $weekStart = now()->startOfWeek();
        $weekStats = [
            'visits' => \App\VisitTracking::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->where('sales_rep_id', $user->id)
                ->whereBetween('start_time', [$weekStart, now()])
                ->count(),
            'completed' => \App\VisitTracking::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->where('sales_rep_id', $user->id)
                ->where('status', 'completed')
                ->whereBetween('start_time', [$weekStart, now()])
                ->count(),
            'leads' => \App\Lead::where('business_id', $business_id)
                ->where('sales_rep_id', $user->id)
                ->whereBetween('created_at', [$weekStart, now()])
                ->count(),
        ];
        
        // Get this month's stats
        $monthStart = now()->startOfMonth();
        $monthStats = [
            'visits' => \App\VisitTracking::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->where('sales_rep_id', $user->id)
                ->whereBetween('start_time', [$monthStart, now()])
                ->count(),
            'completed' => \App\VisitTracking::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->where('sales_rep_id', $user->id)
                ->where('status', 'completed')
                ->whereBetween('start_time', [$monthStart, now()])
                ->count(),
            'leads' => \App\Lead::where('business_id', $business_id)
                ->where('sales_rep_id', $user->id)
                ->whereBetween('created_at', [$monthStart, now()])
                ->count(),
        ];
        
        // Get current shift status
        $currentShift = \App\SalesRepShift::where('sales_rep_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();
        
        $shiftInfo = null;
        if ($currentShift) {
            $shiftInfo = [
                'is_online' => true,
                'shift_start' => $currentShift->shift_start_time,
                'duration' => $currentShift->shift_start_time->diffForHumans(),
                'status' => 'Online'
            ];
        } else {
            $lastShift = \App\SalesRepShift::where('sales_rep_id', $user->id)
                ->where('status', 'ended')
                ->latest()
                ->first();
            
            $shiftInfo = [
                'is_online' => false,
                'last_shift_end' => $lastShift ? $lastShift->shift_end_time : null,
                'status' => 'Offline'
            ];
        }
        
        return view('sales_commission_agent.show')
                    ->with(compact('user', 'locations', 'permitted_locations', 'view_partials', 'users', 'activities', 'final_commission', 'total_sells', 'monthly_commission', 'current_quarter_commission', 'current_year_commission', 'total_paid', 'pending_payout' ,'transactions','final_commission_payable', 'commission_payouts', 'todayVisits', 'yesterdayVisits', 'weekStats', 'monthStats', 'shiftInfo'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $user = User::where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)
                    ->findOrFail($id);

        $locations = \App\BusinessLocation::where('business_id', $business_id)
                                        ->where('is_b2c', 0)
                                        ->Active()
                                        ->get();
        $permitted_locations = $user->permitted_locations();

        // Load existing B2B customer access
        $user->load('contactAccess');

        return view('sales_commission_agent.edit')
                    ->with(compact('user', 'locations', 'permitted_locations'));
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
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        // Validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'cmmsn_percent' => 'required|numeric|min:0|max:100',
            'max_discount_percent' => 'nullable|numeric|min:0|max:100',
        ];

        // Add login validation if allow_login is checked
        if ($request->has('allow_login') && $request->input('allow_login')) {
            $rules['username'] = 'nullable|string|max:255|unique:users,username,' . $id;
            if ($request->filled('password')) {
                $rules['password'] = 'string|min:6';
            }
        }

        $request->validate($rules);

        if (request()->ajax()) {
            try {
                $input = $request->only([
                    'surname', 'first_name', 'last_name', 'email', 'address', 'contact_no', 'cmmsn_percent',
                    'max_discount_percent', 'dob', 'gender', 'marital_status', 'blood_group', 'contact_number', 'alt_number', 
                    'family_number', 'guardian_name', 'id_proof_name', 'id_proof_number', 
                    'permanent_address', 'current_address', 'status', 'allow_login', 'username', 'password','permanent_address', 'current_address', 'status', 'allow_login', 'username', 'password', 'commission_type', 'percentage_value'
                ]);
                
                // Handle bank details separately as it's a JSON field
                $bank_details = [];
                if ($request->has('bank_details') && is_array($request->input('bank_details'))) {
                    $bank_details = $request->input('bank_details');
                }
                $input['bank_details'] = !empty($bank_details) ? json_encode($bank_details) : null;
                
                $input['cmmsn_percent'] = $this->commonUtil->num_uf($input['cmmsn_percent']);
                if (!empty($input['max_discount_percent'])) {
                    $input['max_discount_percent'] = $this->commonUtil->num_uf($input['max_discount_percent']);
                }
                $business_id = $request->session()->get('user.business_id');

                // Handle login credentials
                if (empty($input['allow_login'])) {
                    $input['username'] = null;
                    $input['password'] = null;
                    $input['allow_login'] = 0;
                } else {
                    $input['allow_login'] = 1;
                    // Only update password if provided
                    if (!empty($input['password'])) {
                        $input['password'] = \Hash::make($input['password']);
                    } else {
                        // Remove password from input if not provided to keep existing password
                        unset($input['password']);
                    }
                    // Generate username if not provided
                    if (empty($input['username'])) {
                        $ref_count = $this->commonUtil->setAndGetReferenceCount('username', $business_id);
                        $input['username'] = $this->commonUtil->generateReferenceNumber('username', $ref_count, $business_id);
                    }
                }

                $user = User::where('id', $id)
                            ->where('business_id', $business_id)
                            ->where('is_cmmsn_agnt', 1)
                            ->first();
                $user->update($input);

                // Grant Location permissions
                $this->commonUtil->giveLocationPermissions($user, $request);

                // Handle B2B customer access - remove existing and add new ones
                \App\UserContactAccess::where('user_id', $user->id)->delete();
                if ($request->has('b2b_customers') && is_array($request->input('b2b_customers'))) {
                    $customer_ids = $request->input('b2b_customers');
                    // Filter out empty values
                    $customer_ids = array_filter($customer_ids, function($id) {
                        return !empty($id);
                    });
                    
                    foreach ($customer_ids as $customer_id) {
                        try {
                            \App\UserContactAccess::create([
                                'user_id' => $user->id,
                                'contact_id' => $customer_id
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Error creating UserContactAccess: ' . $e->getMessage());
                            \Log::error('User ID: ' . $user->id . ', Contact ID: ' . $customer_id);
                        }
                    }
                }

                $output = ['success' => true,
                    'msg' => __('lang_v1.commission_agent_updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                User::where('id', $id)
                    ->where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)
                    ->delete();

                $output = ['success' => true,
                    'msg' => __('lang_v1.commission_agent_deleted_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
    /**
     * Update commission settings for a sales commission agent
     */
    public function updateCommissionSettings(Request $request, $id)
    {
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                
                $user = User::where('id', $id)
                            ->where('business_id', $business_id)
                            ->where('is_cmmsn_agnt', 1)
                            ->first();

                if (!$user) {
                    return ['success' => false, 'msg' => 'Commission agent not found.'];
                }

                $input = $request->only(['cmmsn_percent']);
                
                // Convert percentage to decimal
                if (isset($input['cmmsn_percent'])) {
                    $input['cmmsn_percent'] = $this->commonUtil->num_uf($input['cmmsn_percent']);
                }

                $user->update($input);

                $output = ['success' => true, 'msg' => 'Commission settings updated successfully.'];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                $output = ['success' => false, 'msg' => 'Something went wrong.'];
            }

            return $output;
        }
    }
    /**
     * Update bonus settings for a sales commission agent
     */
    public function updateBonusSettings(Request $request, $id)
    {
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                
                $user = User::where('id', $id)
                            ->where('business_id', $business_id)
                            ->where('is_cmmsn_agnt', 1)
                            ->first();

                if (!$user) {
                    return ['success' => false, 'msg' => 'Commission agent not found.'];
                }

                $input = $request->only([
                    'quarterly_bonus_amount', 'quarterly_sales_target',
                    'yearly_bonus_amount', 'yearly_sales_target',
                ]);

                // Convert amounts to proper format
                if (isset($input['quarterly_bonus_amount'])) {
                    $input['quarterly_bonus_amount'] = $this->commonUtil->num_uf($input['quarterly_bonus_amount']);
                }
                if (isset($input['quarterly_sales_target'])) {
                    $input['quarterly_sales_target'] = $this->commonUtil->num_uf($input['quarterly_sales_target']);
                }
                if (isset($input['yearly_bonus_amount'])) {
                    $input['yearly_bonus_amount'] = $this->commonUtil->num_uf($input['yearly_bonus_amount']);
                }
                if (isset($input['yearly_sales_target'])) {
                    $input['yearly_sales_target'] = $this->commonUtil->num_uf($input['yearly_sales_target']);
                }

                // Update the user with bonus settings
                $user->update($input);

                $output = ['success' => true, 'msg' => 'Bonus settings updated successfully.'];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                $output = ['success' => false, 'msg' => 'Something went wrong, Please try again.'];
            }

            return $output;
        }
    }
    /**
     * Process payout for a sales commission agent
     */
    public function processPayout(Request $request, $id)
    {
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            // dd($request->all());
            // Get the commission agent
            $commission_agent = User::where('business_id', $business_id)
                ->where('is_cmmsn_agnt', 1)
                ->findOrFail($id);

            // Validate the request

            $request->validate([
                'payout_amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required',
                'note' => 'nullable|string|max:255'
            ]);
            $transaction_date = Carbon::now()->format('Y-m-d H:i:s');
            $request->merge(['transaction_date' => $transaction_date]);
            
            DB::beginTransaction();

            // Create commission payout transaction
            $transaction_data = [
                'business_id' => $business_id,
                'location_id' => $request->input('location_id', 1), // Default location
                'type' => 'commission_payout',
                'status' => 'final',
                'payment_status' => 'paid',
                'contact_id' => null, // Commission agent is not a contact
                'transaction_date' => $request->input('transaction_date'),
                'final_total' => $this->commonUtil->num_uf($request->input('payout_amount')),
                'total_before_tax' => $this->commonUtil->num_uf($request->input('payout_amount')),
                'tax_amount' => 0,
                'additional_notes' => $request->input('note'),
                'created_by' => auth()->user()->id,
                'commission_agent' => $commission_agent->id,
                'expense_for' => $commission_agent->id, // Store commission agent ID in expense_for field
            ];

            // Generate reference number
            $ref_count = $this->commonUtil->setAndGetReferenceCount('commission_payout', $business_id);
            $transaction_data['ref_no'] = $this->commonUtil->generateReferenceNumber('commission_payout', $ref_count, $business_id);

            // Create the transaction
            $transaction = Transaction::create($transaction_data);

            // Create payment record
            $payment_data = [
                'transaction_id' => $transaction->id,
                'business_id' => $business_id,
                'amount' => $this->commonUtil->num_uf($request->input('payout_amount')),
                'method' => $request->input('payment_method'),
                'paid_on' => $request->input('transaction_date'),
                'created_by' => auth()->user()->id,
                'payment_for' => $commission_agent->id,
                'note' => $request->input('note'),
            ];

            // Handle additional payment method details
            if ($request->input('payment_method') == 'bank_transfer') {
                $payment_data['bank_account_number'] = $request->input('bank_account_number');
            } elseif ($request->input('payment_method') == 'cheque') {
                $payment_data['cheque_number'] = $request->input('cheque_number');
            } elseif ($request->input('payment_method') == 'card') {
                $payment_data['card_number'] = $request->input('card_number');
                $payment_data['card_holder_name'] = $request->input('card_holder_name');
                $payment_data['card_transaction_number'] = $request->input('card_transaction_number');
                $payment_data['card_type'] = $request->input('card_type');
                $payment_data['card_month'] = $request->input('card_month');
                $payment_data['card_year'] = $request->input('card_year');
                $payment_data['card_security'] = $request->input('card_security');
            }

            // Generate payment reference number
            $payment_ref_count = $this->commonUtil->setAndGetReferenceCount('commission_payout_payment', $business_id);
            $payment_data['payment_ref_no'] = $this->commonUtil->generateReferenceNumber('commission_payout_payment', $payment_ref_count, $business_id);

            // Create payment
            TransactionPayment::create($payment_data);

            // Log activity
            $this->commonUtil->activityLog($transaction, 'added');

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.commission_payout_processed_successfully')
            ];

        } catch (\Exception $e) {
            DB::rollback();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
                'error line' => $e->getLine(),
                'error file' => $e->getFile()
            ];
        }

        if ($request->ajax()) {
            return $output;
        }

        return redirect()->back()->with('status', $output);
    }
}
