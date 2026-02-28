<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Lead;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use App\Notifications\LeadCreatedNotification;
use DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
class LeadController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @param ModuleUtil $moduleUtil
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
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $leads = Lead::where('business_id', $business_id)
                ->with(['creator', 'visitor'])
                ->select('leads.*');

            // Admin can see all leads

            return Datatables::of($leads)
                ->addColumn('reference_no', function ($row) {
                    return $row->reference_no ?? 'N/A';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->creator ? $row->creator->first_name . ' ' . $row->creator->last_name : 'N/A';
                })
                ->addColumn('visited_by', function ($row) {
                    return $row->visitor ? $row->visitor->first_name . ' ' . $row->visitor->last_name : 'N/A';
                })
                ->addColumn('status', function ($row) {
                    $statusClass = $row->status == 'visited' ? 'success' : 'warning';
                    return '<span class="label label-' . $statusClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div">';
                    
                    $html .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info btn-modal" data-href="' . action([\App\Http\Controllers\LeadController::class, 'show'], [$row->id]) . '" data-container=".lead_modal" title="' . __("messages.view") . '">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                              </button>';
                    
                    $html .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary btn-modal" data-href="' . action([\App\Http\Controllers\LeadController::class, 'edit'], [$row->id]) . '" data-container=".lead_modal" title="' . __("messages.edit") . '">
                                <i class="fa fa-edit" aria-hidden="true"></i>
                              </button>';
                    
                    $html .= '<a href="' . action([\App\Http\Controllers\LeadController::class, 'destroy'], [$row->id]) . '" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_lead" title="' . __("messages.delete") . '">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                              </a>';

                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('lead.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id'))) {
abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // Get users for assignment dropdowns
        $users = \App\User::where('business_id', $business_id)
            ->select('id', 'first_name', 'last_name', 'username')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name . ' (' . $user->username . ')'
                ];
            });

        return view('lead.create')
            ->with(compact('business_locations', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate email and phone uniqueness (only if columns exist)
            $business_id = $request->session()->get('user.business_id');
            
            $rules = [];
            
            // Only validate contact_email if column exists
            if (\Schema::hasColumn('leads', 'contact_email')) {
                $rules['contact_email'] = [
                    'nullable',
                    'email',
                    Rule::unique('leads', 'contact_email')
                        ->where('business_id', $business_id)
                        ->whereNotNull('contact_email')
                ];
            }
            
            // Only validate contact_phone if column exists
            if (\Schema::hasColumn('leads', 'contact_phone')) {
                $rules['contact_phone'] = [
                    'nullable', // Changed from 'required' to 'nullable'
                    'string',
                    'min:10',
                    'max:10',
                    Rule::unique('leads', 'contact_phone')
                        ->where('business_id', $business_id)
                        ->whereNotNull('contact_phone')
                ];
            }
            
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $output = [
                    'success' => false,
                    'msg' => $validator->errors()->first()
                ];
                return $output;
            }

            $input = $request->only([
                'store_name', 'address_line_1', 'address_line_2', 'state', 'city', 'country', 'zip_code', 'full_address',
                'contact_name', 'contact_email', 'contact_phone', 'company_name', 'website',
                'lead_source', 'assigned_to', 'sales_rep_id', 'lead_status', 'priority', 'funnel_stage',
                'next_follow_up_date', 'last_contact_date', 'notes', 'internal_notes',
                'visit_proof_url', 'latitude', 'longitude', 'location_accuracy',
                'estimated_value', 'actual_value', 'currency', 'lead_score', 'rating',
                'industry', 'company_size', 'preferred_contact_method',
                'best_contact_time_start', 'best_contact_time_end', 'utm_source', 'utm_medium',
                'utm_campaign', 'referral_source', 'is_qualified', 'is_hot_lead',
                'requires_immediate_attention'
            ]);
            
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = auth()->user()->id;
            
            // Set default values
            $input['status'] = 'pending';
            $input['lead_status'] = $input['lead_status'] ?? 'new';
            $input['priority'] = $input['priority'] ?? 'medium';
            $input['funnel_stage'] = $input['funnel_stage'] ?? 'initial_contact';
            $input['lead_source'] = $input['lead_source'] ?? 'admin_panel';
            $input['currency'] = $input['currency'] ?? 'USD';
            $input['preferred_contact_method'] = $input['preferred_contact_method'] ?? 'phone';
            $input['lead_score'] = $input['lead_score'] ?? 0;
            $input['is_qualified'] = $input['is_qualified'] ?? false;
            $input['is_hot_lead'] = $input['is_hot_lead'] ?? false;
            $input['requires_immediate_attention'] = $input['requires_immediate_attention'] ?? false;
            
            // Handle tags if provided
            if ($request->has('tags') && is_string($request->tags)) {
                $input['tags'] = json_decode($request->tags, true);
            }
            
            // Handle custom fields if provided
            if ($request->has('custom_fields') && is_string($request->custom_fields)) {
                $input['custom_fields'] = json_decode($request->custom_fields, true);
            }
            
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

            // Preserve full_address if provided (e.g., from Google autocomplete), otherwise construct it
            if (empty($input['full_address'])) {
                $addressComponents = array_filter([
                    $input['address_line_1'] ?? '',
                    $input['address_line_2'] ?? '',
                    $input['city'] ?? '',
                    $input['state'] ?? '',
                    $input['country'] ?? '',
                    $input['zip_code'] ?? ''
                ]);
                
                $input['full_address'] = implode(', ', $addressComponents);
            }

            $lead = Lead::create($input);

            // Send notifications
            $this->sendLeadNotifications($lead);

            $output = [
                'success' => true,
                'data' => $lead,
                'msg' => __("lang_v1.success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
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
        if (!auth()->user()->hasRole('Admin#' . session('business.id'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        // Load lead with all relationships for complete details
        $lead = Lead::where('business_id', $business_id)
            ->with([
                'creator' => function($query) {
                    $query->select('id', 'first_name', 'last_name', 'email', 'contact_number');
                },
                'assignedTo' => function($query) {
                    $query->select('id', 'first_name', 'last_name', 'email', 'contact_number');
                },
                'salesRep' => function($query) {
                    $query->select('id', 'first_name', 'last_name', 'email', 'contact_number');
                },
                'visitor' => function($query) {
                    $query->select('id', 'first_name', 'last_name', 'email', 'contact_number');
                }
            ])
            ->findOrFail($id);

        // Get visit tracking data for this lead (only if table exists)
        $visitRecords = collect(); // Empty collection by default
        if (\Schema::hasTable('visit_tracking')) {
            $visitRecords = \DB::table('visit_tracking')
                ->join('users', 'visit_tracking.sales_rep_id', '=', 'users.id')
                ->where('visit_tracking.lead_id', $id)
                ->where('visit_tracking.business_id', $business_id)
                ->select(
                    'visit_tracking.*',
                    'users.first_name',
                    'users.last_name',
                    'users.username'
                )
                ->orderBy('visit_tracking.start_time', 'desc')
                ->get();
        }

        // Get tickets for THIS SPECIFIC LEAD ONLY (filtered by lead_id)
        // This ensures only tickets belonging to the current lead are displayed
        $tickets = \App\Ticket::where('lead_id', $id)
            ->where('lead_id', '!=', null) // Extra safety: ensure lead_id is not null
            ->with([
                'user:id,first_name,last_name',
                'closedBy:id,first_name,last_name',
                'lead:id,reference_no,store_name' // Include lead info for verification
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate ticket statistics for THIS LEAD ONLY
        $ticketStats = [
            'total' => $tickets->count(),
            'open' => $tickets->where('status', 'open')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'pending' => $tickets->where('status', 'pending')->count(),
            'resolved' => $tickets->where('status', 'resolved')->count(),
            'closed' => $tickets->where('status', 'closed')->count(),
        ];

        return view('lead.show_modal')
            ->with(compact('lead', 'visitRecords', 'tickets', 'ticketStats'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $lead = Lead::where('business_id', $business_id)->findOrFail($id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // Get users for assignment dropdowns
        $users = \App\User::where('business_id', $business_id)
            ->select('id', 'first_name', 'last_name', 'username')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name . ' (' . $user->username . ')'
                ];
            });

        return view('lead.edit_modal')
            ->with(compact('lead', 'business_locations', 'users'));
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
        if (!auth()->user()->hasRole('Admin#' . session('business.id'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $lead = Lead::where('business_id', $business_id)->findOrFail($id);

            // Validate email and phone uniqueness (only if columns exist)
            $rules = [];
            
            // Only validate contact_email if column exists
            if (\Schema::hasColumn('leads', 'contact_email')) {
                $rules['contact_email'] = [
                    'nullable',
                    'email',
                    Rule::unique('leads', 'contact_email')
                        ->where('business_id', $business_id)
                        ->whereNotNull('contact_email')
                        ->ignore($lead->id) // Ignore the current record
                ];
            }
            
            // Only validate contact_phone if column exists
            if (\Schema::hasColumn('leads', 'contact_phone')) {
                $rules['contact_phone'] = [
                    'nullable', // Changed from 'required' to 'nullable'
                    'string',
                    'min:10',
                    'max:10',
                    Rule::unique('leads', 'contact_phone')
                        ->where('business_id', $business_id)
                        ->whereNotNull('contact_phone')
                        ->ignore($lead->id) // Ignore the current record
                ];
            }
            
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $output = [
                    'success' => false,
                    'msg' => $validator->errors()->first()
                ];
                return $output;
            }

            $input = $request->only([
                'store_name', 'address_line_1', 'address_line_2', 'state', 'city', 'country', 'zip_code', 'full_address', 'status',
                'contact_name', 'contact_email', 'contact_phone', 'company_name', 'website',
                'lead_source', 'assigned_to', 'sales_rep_id', 'lead_status', 'priority', 'funnel_stage',
                'next_follow_up_date', 'last_contact_date', 'notes', 'internal_notes',
                'visit_proof_url', 'latitude', 'longitude', 'location_accuracy',
                'estimated_value', 'actual_value', 'currency', 'lead_score', 'rating',
                'industry', 'company_size', 'preferred_contact_method',
                'best_contact_time_start', 'best_contact_time_end', 'utm_source', 'utm_medium',
                'utm_campaign', 'referral_source', 'is_qualified', 'is_hot_lead',
                'requires_immediate_attention'
            ]);

            // Handle tags if provided
            if ($request->has('tags') && is_string($request->tags)) {
                $input['tags'] = json_decode($request->tags, true);
            }
            
            // Handle custom fields if provided
            if ($request->has('custom_fields') && is_string($request->custom_fields)) {
                $input['custom_fields'] = json_decode($request->custom_fields, true);
            }

            // Preserve full_address if provided (e.g., from Google autocomplete), otherwise construct it
            if (empty($input['full_address'])) {
                $addressComponents = array_filter([
                    $input['address_line_1'] ?? '',
                    $input['address_line_2'] ?? '',
                    $input['city'] ?? '',
                    $input['state'] ?? '',
                    $input['country'] ?? '',
                    $input['zip_code'] ?? ''
                ]);
                
                $input['full_address'] = implode(', ', $addressComponents);
            }

            $lead->update($input);

            $output = [
                'success' => true,
                'data' => $lead,
                'msg' => __("lang_v1.success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $lead = Lead::where('business_id', $business_id)->findOrFail($id);
            $lead->delete();

            $output = [
                'success' => true,
                'msg' => __("lang_v1.success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Get leads for dropdown
     *
     * @return \Illuminate\Http\Response
     */
    public function getLeadsDropdown()
    {
        $business_id = request()->session()->get('user.business_id');
        $leads = Lead::leadsDropdown($business_id);

        return response()->json($leads);
    }

    /**
     * Send notifications for lead creation
     *
     * @param Lead $lead
     * @return void
     */
    private function sendLeadNotifications(Lead $lead)
    {
        try {
            $creator = auth()->user();
            $assignedUser = null;
            
            // Get assigned user if exists
            if ($lead->assigned_to) {
                $assignedUser = User::find($lead->assigned_to);
            }

            // Notify assigned user
            if ($assignedUser && $assignedUser->id !== $creator->id) {
                $assignedUser->notify(new LeadCreatedNotification($lead, $creator, $assignedUser));
            }

            // Notify admins
            $admins = User::where('business_id', $lead->business_id)
                ->whereHas('roles', function($query) use ($lead) {
                    $query->where('name', 'Admin#' . $lead->business_id);
                })
                ->get();

            foreach ($admins as $admin) {
                if ($admin->id !== $creator->id) {
                    $admin->notify(new LeadCreatedNotification($lead, $creator, $assignedUser));
                }
            }

            // Notify sales rep if different from creator and assigned user
            if ($lead->sales_rep_id && $lead->sales_rep_id !== $creator->id && $lead->sales_rep_id !== ($assignedUser ? $assignedUser->id : null)) {
                $salesRep = User::find($lead->sales_rep_id);
                if ($salesRep) {
                    $salesRep->notify(new LeadCreatedNotification($lead, $creator, $assignedUser));
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to send lead notifications: ' . $e->getMessage());
        }
    }

    /**
     * Display visit tracking page
     *
     * @return \Illuminate\Http\Response
     */
    public function visitTracking(Request $request)
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        // Handle AJAX request for DataTables
        if ($request->ajax()) {
            // Fetch visit tracking records (only if table exists)
            $visitRecords = null;
            if (\Schema::hasTable('visit_tracking')) {
                $visitRecords = \App\VisitTracking::with(['salesRep', 'lead'])
                    ->where('visit_tracking.business_id', $business_id)
                    ->select('visit_tracking.*', \DB::raw("'visit' as record_type"));
            }

            // Fetch tickets
            $tickets = \App\Ticket::with(['user', 'lead'])
                ->whereHas('lead', function($query) use ($business_id) {
                    $query->where('business_id', $business_id);
                })
                ->select('tickets.*', \DB::raw("'ticket' as record_type"));

            // Apply filters to visits (only if table exists)
            if ($visitRecords && $request->has('sales_rep_id') && $request->sales_rep_id != '') {
                $visitRecords->where('visit_tracking.sales_rep_id', $request->sales_rep_id);
                $tickets->where('tickets.user_id', $request->sales_rep_id);
            }

            if ($visitRecords && $request->has('status') && $request->status != '') {
                $visitRecords->where('visit_tracking.status', $request->status);
                $tickets->where('tickets.status', $request->status);
            }

            if ($visitRecords && $request->has('date') && $request->date != '') {
                $visitRecords->whereDate('visit_tracking.start_time', $request->date);
                $tickets->whereDate('tickets.created_at', $request->date);
            }

            // Combine visits and tickets
            $visitData = $visitRecords ? $visitRecords->get()->map(function($visit) {
                return (object)[
                    'id' => $visit->id,
                    'record_type' => 'visit',
                    'lead' => $visit->lead,
                    'salesRep' => $visit->salesRep,
                    'start_time' => $visit->start_time,
                    'duration' => $visit->duration,
                    'status' => $visit->status,
                    'reference_no' => $visit->lead->reference_no ?? 'N/A',
                    'ticket_reference' => null,
                    'ticket_description' => null,
                    'sort_date' => $visit->start_time,
                ];
            }) : collect(); // Return empty collection if no visit records
            
            $ticketData = $tickets->get()->map(function($ticket) {
                return (object)[
                    'id' => $ticket->id,
                    'record_type' => 'ticket',
                    'lead' => $ticket->lead,
                    'salesRep' => $ticket->user,
                    'start_time' => null,
                    'duration' => null,
                    'status' => $ticket->status,
                    'reference_no' => $ticket->lead->reference_no ?? 'N/A',
                    'ticket_reference' => $ticket->reference_no ?? 'TICKET-' . $ticket->id,
                    'ticket_description' => $ticket->ticket_description,
                    'sort_date' => $ticket->created_at,
                ];
            });
            
            // Merge visit and ticket data
            $combined = $visitData->merge($ticketData)->sortByDesc('sort_date')->values();

            return Datatables::of($combined)
                ->addColumn('type', function ($row) {
                    if ($row->record_type == 'ticket') {
                        return '<span class="badge badge-warning" style="font-size: 0.7rem;">Ticket</span>';
                    }
                    return '<span class="badge badge-info" style="font-size: 0.7rem;">Visit</span>';
                })
                ->addColumn('reference_no', function ($row) {
                    if ($row->record_type == 'ticket') {
                        return $row->ticket_reference ?? 'N/A';
                    }
                    return $row->reference_no;
                })
                ->addColumn('store_name', function ($row) {
                    return '<strong>' . ($row->lead->store_name ?? 'N/A') . '</strong>';
                })
                ->addColumn('full_address', function ($row) {
                    return $row->lead->full_address ?? 'N/A';
                })
                ->addColumn('sales_rep_name', function ($row) {
                    if (!$row->salesRep) return 'N/A';
                    return ($row->salesRep->first_name ?? '') . ' ' . ($row->salesRep->last_name ?? '');
                })
                ->addColumn('formatted_start_time', function ($row) {
                    if ($row->record_type == 'ticket') {
                        return \Carbon\Carbon::parse($row->sort_date)->format('M d, Y h:i A');
                    }
                    return $row->start_time ? \Carbon\Carbon::parse($row->start_time)->format('M d, Y h:i A') : 'N/A';
                })
                ->addColumn('formatted_duration', function ($row) {
                    if ($row->record_type == 'ticket') {
                        return '-';
                    }
                    return $row->duration ? $row->duration . 'm' : 'N/A';
                })
                ->addColumn('ticket_info', function ($row) {
                    if ($row->record_type == 'ticket') {
                        return '<span style="font-size: 0.85rem;">' . htmlspecialchars(substr($row->ticket_description, 0, 50)) . (strlen($row->ticket_description) > 50 ? '...' : '') . '</span>';
                    }
                    return '-';
                })
                ->addColumn('status_badge', function ($row) {
                    $statusMap = [
                        // Visit statuses
                        'completed' => '<span class="status-badge status-visited">Visited</span>',
                        'in_progress' => '<span class="status-badge status-in-progress">In Progress</span>',
                        'missing_proof' => '<span class="status-badge status-missing-proof">Missing Proof</span>',
                        'scheduled' => '<span class="status-badge status-scheduled">Scheduled</span>',
                        'pending' => '<span class="status-badge status-pending">Pending</span>',
                        // Ticket statuses
                        'open' => '<span class="status-badge" style="background-color: #dc3545; color: white;">Open</span>',
                        'closed' => '<span class="status-badge" style="background-color: #28a745; color: white;">Closed</span>',
                        'resolved' => '<span class="status-badge" style="background-color: #28a745; color: white;">Resolved</span>',
                    ];
                    
                    return $statusMap[$row->status] ?? '<span class="status-badge status-pending">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['type', 'store_name', 'status_badge', 'ticket_info'])
                ->make(true);
        }
        
        // Get users for assignment dropdowns - same logic as Add Lead modal
        $salesReps = \App\User::where('business_id', $business_id)
            ->select('id', 'first_name', 'last_name', 'username')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name . ' (' . $user->username . ')'
                ];
            });
        
        // Get leads for the current user (sales rep)
        $leads = Lead::where('business_id', $business_id);
        
        // Check if columns exist before querying
        if (\Schema::hasColumn('leads', 'assigned_to')) {
            $leads->where(function($query) {
                $query->where('assigned_to', auth()->user()->id);
                if (\Schema::hasColumn('leads', 'sales_rep_id')) {
                    $query->orWhere('sales_rep_id', auth()->user()->id);
                }
                $query->orWhere('created_by', auth()->user()->id);
            });
        } else {
            // Fallback to created_by only if assigned_to doesn't exist
            $leads->where('created_by', auth()->user()->id);
        }
        
        $leads = $leads->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get visit statistics for the stats cards (only if table exists)
        $totalVisits = 0;
        $completedVisits = 0;
        $inProgressVisits = 0;
        $missingProofVisits = 0;
        $avgDuration = 0;
        
        if (\Schema::hasTable('visit_tracking')) {
            $visitStats = \App\VisitTracking::where('business_id', $business_id);
            
            $totalVisits = $visitStats->count();
            $completedVisits = (clone $visitStats)->where('status', 'completed')->count();
            $inProgressVisits = (clone $visitStats)->where('status', 'in_progress')->count();
            $missingProofVisits = (clone $visitStats)->where('status', 'missing_proof')->count();
            $avgDuration = (clone $visitStats)->where('duration', '>', 0)->avg('duration');
            $avgDuration = $avgDuration ? round($avgDuration) : 0;
        }

        return view('lead.visit_tracking', compact('leads', 'salesReps', 'totalVisits', 'completedVisits', 'inProgressVisits', 'missingProofVisits', 'avgDuration'));
    }

    /**
     * Store visit record
     * DEPRECATED: Use storeTrack() instead
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeVisit(Request $request)
    {
        // Redirect to the consolidated method
        return $this->storeTrack($request);
    }

    /**
     * Mark lead as visited
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function markVisited(Request $request)
    {
        $user = auth()->user();
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $user->hasRole('Admin#' . $business_id);

        // Allow admin or the assigned sales rep
        if (!$is_admin && (!isset($request->sales_rep_id) || $request->sales_rep_id != $user->id)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $lead = Lead::findOrFail($request->lead_id);
            
            $lead->update([
                'status' => 'visited',
                'last_contact_date' => $request->visit_date,
                'next_follow_up_date' => $request->next_follow_up,
                'notes' => $request->notes
            ]);

            $output = [
                'success' => true,
                'msg' => 'Lead marked as visited successfully!'
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Schedule follow-up for lead
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function scheduleFollowup(Request $request)
    {
        $user = auth()->user();
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $user->hasRole('Admin#' . $business_id);

        if (!$is_admin) {
            // Allow sales rep to schedule follow-up for their own leads
            $lead = Lead::findOrFail($request->lead_id);
            if ($lead->assigned_to != $user->id && $lead->sales_rep_id != $user->id && $lead->created_by != $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        try {
            $lead = Lead::findOrFail($request->lead_id);
            
            $followupDate = $request->followup_date;
            if ($request->followup_time) {
                $followupDate .= ' ' . $request->followup_time;
            }
            
            $lead->update([
                'next_follow_up_date' => $followupDate,
                'notes' => $request->notes
            ]);

            $output = [
                'success' => true,
                'msg' => 'Follow-up scheduled successfully!'
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Create track entry
     * DEPRECATED: Use storeTrack() instead
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createTrack(Request $request)
    {
        // Redirect to the consolidated method
        return $this->storeTrack($request);
    }

    /**
     * Get visit details
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function visitDetails($id)
    {
        $user = auth()->user();
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $user->hasRole('Admin#' . $business_id);

        try {
            $visit = \App\VisitTracking::with(['salesRep', 'lead'])
                ->where('business_id', $business_id)
                ->findOrFail($id);

            // Allow admin or the assigned sales rep
            if (!$is_admin && $visit->sales_rep_id != $user->id) {
                abort(403, 'Unauthorized action.');
            }

            if (!$visit) {
                return '<div class="alert alert-danger">Visit not found.</div>';
            }

            return view('lead.visit_details_modal', compact('visit'))->render();

        } catch (\Exception $e) {
            return '<div class="alert alert-danger">Error loading visit details: ' . $e->getMessage() . '</div>';
        }
    }

    /**
     * Store a new visit tracking entry
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeTrack(Request $request)
    {
        $user = auth()->user();
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $user->hasRole('Admin#' . $business_id);

        // Allow admin or sales rep to create their own visit records
        if (!$is_admin && $request->sales_rep_id != $user->id) {
            abort(403, 'Unauthorized action. You can only create visit records for yourself.');
        }

        try {
            // Validate required fields with enhanced validation
            $validated = $request->validate([
                'sales_rep_id' => 'required|exists:users,id',
                'lead_id' => 'required|exists:leads,id',
                'start_time' => 'required|date',
                'duration' => 'nullable|integer|min:0',
                'status' => 'required|in:scheduled,in_progress,completed,cancelled,rescheduled,pending,missing_proof',
                'visit_type' => 'nullable|in:initial,follow_up,demo,closing,meeting,support',
                // File validation
                'location_proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
                'photo_proof_file.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'signature_proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // 2MB
                'video_proof_file' => 'nullable|file|mimes:mp4,mov,avi|max:51200', // 50MB
                // GPS validation
                'checkin_latitude' => 'nullable|numeric|between:-90,90',
                'checkin_longitude' => 'nullable|numeric|between:-180,180',
                'checkout_latitude' => 'nullable|numeric|between:-90,90',
                'checkout_longitude' => 'nullable|numeric|between:-180,180',
                'checkout_time' => 'nullable|date',
                'distance_travelled' => 'nullable|numeric|min:0',
                'remarks' => 'nullable|string|max:1000'
            ]);

            // Handle file uploads
            $location_proof_path = null;
            $photo_proof_paths = [];
            $signature_proof_path = null;
            $video_proof_path = null;

            // Location proof
            if ($request->hasFile('location_proof_file')) {
                $location_proof_path = $request->file('location_proof_file')->store('visit_proofs/location', 'public');
            }

            // Photo proof (multiple files)
            if ($request->hasFile('photo_proof_file')) {
                foreach ($request->file('photo_proof_file') as $file) {
                    $photo_proof_paths[] = $file->store('visit_proofs/photos', 'public');
                }
            }

            // Signature proof
            if ($request->hasFile('signature_proof_file')) {
                $signature_proof_path = $request->file('signature_proof_file')->store('visit_proofs/signature', 'public');
            }

            // Video proof
            if ($request->hasFile('video_proof_file')) {
                $video_proof_path = $request->file('video_proof_file')->store('visit_proofs/video', 'public');
            }

            // Create visit tracking entry using Eloquent model
            $visitTracking = new \App\VisitTracking();
            $visitTracking->business_id = $business_id;
            $visitTracking->sales_rep_id = $request->sales_rep_id;
            $visitTracking->lead_id = $request->lead_id;
            $visitTracking->start_time = $request->start_time;
            $visitTracking->duration = $request->duration;
            $visitTracking->status = $request->status;
            $visitTracking->visit_type = $request->visit_type ?? 'initial';
            $visitTracking->location_proof = !empty($location_proof_path);
            $visitTracking->photo_proof = !empty($photo_proof_paths);
            $visitTracking->signature_proof = !empty($signature_proof_path);
            $visitTracking->video_proof = !empty($video_proof_path);
            $visitTracking->location_proof_path = $location_proof_path;
            // FIX: Don't JSON encode - model will handle it with array cast
            $visitTracking->photo_proof_paths = $photo_proof_paths;
            $visitTracking->signature_proof_path = $signature_proof_path;
            $visitTracking->video_proof_path = $video_proof_path;
            
            // GPS coordinates
            $visitTracking->checkin_latitude = $request->checkin_latitude;
            $visitTracking->checkin_longitude = $request->checkin_longitude;
            $visitTracking->checkout_latitude = $request->checkout_latitude;
            $visitTracking->checkout_longitude = $request->checkout_longitude;
            $visitTracking->checkout_time = $request->checkout_time;
            $visitTracking->distance_travelled = $request->distance_travelled;
            
            $visitTracking->remarks = $request->remarks;
            $visitTracking->created_by = $user->id;
            $visitTracking->save();

            $output = [
                'success' => true,
                'msg' => 'Visit tracking entry created successfully!'
            ];

        } catch (\Illuminate\Validation\ValidationException $e) {
            $output = [
                'success' => false,
                'msg' => 'Validation Error: ' . implode(', ', $e->validator->errors()->all())
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return response()->json($output);
    }
}