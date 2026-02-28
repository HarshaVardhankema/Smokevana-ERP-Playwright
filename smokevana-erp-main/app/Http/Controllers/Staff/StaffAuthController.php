<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Product;
use App\StaffAuth;
use App\PickersActivity;
use App\VerifierActivity;
use App\Transaction;
use App\TransactionSellLine;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class StaffAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Accept both 'email' and 'username' fields
            $loginField = $request->email ?? $request->username;
            $password = $request->password;
            
            if (empty($loginField) || empty($password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email/Username and password are required',
                ]);
            }
            
            $staff = StaffAuth::where('email', $loginField)
                ->orWhere('username', $loginField)
                ->first();
            if ($staff && Hash::check($password, $staff->password)) {
                if ($staff->allow_login == false) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not Approved!',
                    ]);
                }
                $activity = PickersActivity::where('user_id', $staff->id)->first();
                $data = [
                    'id' => $staff->id,
                    'surname'=>$staff->surname??'',
                    'first_name'=>$staff->first_name,
                    'last_name'=>$staff->last_name,
                    'username'=>$staff->username,
                    'email'=>$staff->email,
                    'is_active'=>$activity->is_active??false,
                    'is_online'=>$staff->is_online??false,
                    'max_discount_percent'=>$staff->max_discount_percent??0,
                ];
                $token = JWTAuth::fromUser($staff);
                return response()->json([
                    'status' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'data' =>$data,
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'login function failed', 'error' => $th->getMessage()]);
        }
    }
    public function profile(Request $request)
    {
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $activity = PickersActivity::where('user_id', $staff->id)->first();
            $verifierActivity = VerifierActivity::where('user_id', $staff->id)->first();
            
            return response()->json([
                'status' => true,
                'message' => 'Staff information retrieved successfully',
                'data' => [
                    'id' => $staff->id,
                    'surname' => $staff->surname ?? '',
                    'first_name' => $staff->first_name,
                    'last_name' => $staff->last_name,
                    'full_name' => trim(($staff->surname ?? '') . ' ' . $staff->first_name . ' ' . $staff->last_name),
                    'username' => $staff->username,
                    'email' => $staff->email,
                    'contact_no' => $staff->contact_no ?? '',
                    'alt_number' => $staff->alt_number ?? '',
                    'family_number' => $staff->family_number ?? '',
                    'facebook_link' => $staff->facebook_link ?? '',
                    'twitter_link' => $staff->twitter_link ?? '',
                    'social_media_1' => $staff->social_media_1 ?? '',
                    'social_media_2' => $staff->social_media_2 ?? '',
                    'custom_field_1' => $staff->custom_field_1 ?? '',
                    'custom_field_2' => $staff->custom_field_2 ?? '',
                    'custom_field_3' => $staff->custom_field_3 ?? '',
                    'custom_field_4' => $staff->custom_field_4 ?? '',
                    'guardian_name' => $staff->guardian_name ?? '',
                    'id_proof_name' => $staff->id_proof_name ?? '',
                    'id_proof_number' => $staff->id_proof_number ?? '',
                    'permanent_address' => $staff->permanent_address ?? '',
                    'current_address' => $staff->current_address ?? '',
                    'max_discount_percent' => $staff->max_discount_percent ?? 0,
                    'is_active' => $activity->is_active ?? false,
                    'is_active_verifier' => $verifierActivity->is_active ?? false,
                    'is_online' => $staff->is_online ?? false,
                    'last_online_at' => $staff->last_online_at ? $staff->last_online_at->format('Y-m-d H:i:s') : null,
                    'allow_login' => $staff->allow_login,
                    'business_id' => $staff->business_id,
                    'created_at' => $staff->created_at ? $staff->created_at->format('Y-m-d H:i:s') : null,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Profile retrieval failed', 'error' => $th->getMessage()]);
        }
    }

    /**
     * Get today's activities for the staff member
     */
    public function getTodayActivities(Request $request)
    {
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $today = now()->startOfDay();
            $todayEnd = now()->endOfDay();
            
            // Get today's visits
            $visits = \App\VisitTracking::where('sales_rep_id', $staff->id)
                ->whereBetween('start_time', [$today, $todayEnd])
                ->with(['lead:id,store_name,company_name,contact_name,contact_phone'])
                ->orderBy('start_time', 'desc')
                ->get()
                ->map(function($visit) {
                    $lead = $visit->lead;
                    return [
                        'id' => $visit->id,
                        'lead_id' => $visit->lead_id,
                        'lead_name' => $lead ? ($lead->store_name ?? $lead->company_name ?? 'N/A') : 'N/A',
                        'contact_name' => $lead ? ($lead->contact_name ?? '') : '',
                        'contact_phone' => $lead ? ($lead->contact_phone ?? '') : '',
                        'start_time' => $visit->start_time->format('Y-m-d H:i:s'),
                        'start_time_formatted' => $visit->start_time->format('h:i A'),
                        'checkout_time' => $visit->checkout_time ? $visit->checkout_time->format('Y-m-d H:i:s') : null,
                        'checkout_time_formatted' => $visit->checkout_time ? $visit->checkout_time->format('h:i A') : null,
                        'duration' => $visit->duration,
                        'status' => $visit->status,
                        'visit_type' => $visit->visit_type,
                        'remarks' => $visit->remarks
                    ];
                });
            
            // Get today's leads created/updated
            $leadsCreated = \App\Lead::where('created_by', $staff->id)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->count();
            
            $leadsUpdated = \App\Lead::where('created_by', $staff->id)
                ->whereBetween('updated_at', [$today, $todayEnd])
                ->where('created_at', '<', $today)
                ->count();
            
            // Get today's tickets
            $tickets = \App\Ticket::where('user_id', $staff->id)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->with(['lead:id,store_name,company_name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($ticket) {
                    $lead = $ticket->lead;
                    return [
                        'id' => $ticket->id,
                        'reference_no' => $ticket->reference_no,
                        'lead_id' => $ticket->lead_id,
                        'lead_name' => $lead ? ($lead->store_name ?? $lead->company_name ?? 'N/A') : 'N/A',
                        'description' => $ticket->ticket_description,
                        'issue_type' => $ticket->issue_type ?? 'general',
                        'status' => $ticket->status,
                        'priority' => $ticket->issue_priority ?? 'medium',
                        'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                        'created_at_formatted' => $ticket->created_at->format('h:i A')
                    ];
                });
            
            // Get today's orders/sells
            $orders = Transaction::where('created_by', $staff->id)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->whereIn('type', ['sell', 'sales_order'])
                ->select('id', 'invoice_no', 'final_total', 'status', 'type', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'invoice_no' => $order->invoice_no,
                        'final_total' => (float) $order->final_total,
                        'status' => $order->status,
                        'type' => $order->type,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                        'created_at_formatted' => $order->created_at->format('h:i A')
                    ];
                });
            
            return response()->json([
                'status' => true,
                'message' => 'Today activities retrieved successfully',
                'data' => [
                    'date' => now()->format('Y-m-d'),
                    'date_formatted' => now()->format('l, F d, Y'),
                    'summary' => [
                        'total_visits' => $visits->count(),
                        'completed_visits' => $visits->where('status', 'completed')->count(),
                        'in_progress_visits' => $visits->where('status', 'in_progress')->count(),
                        'leads_created' => $leadsCreated,
                        'leads_updated' => $leadsUpdated,
                        'tickets_created' => $tickets->count(),
                        'orders_created' => $orders->count(),
                        'total_sales' => (float) $orders->sum('final_total')
                    ],
                    'visits' => $visits,
                    'tickets' => $tickets,
                    'orders' => $orders
                ]
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, 
                'message' => 'Failed to retrieve today activities', 
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics for the staff member
     * Query params: filter=today|yesterday|week|month|year or start_date & end_date
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            
            $filter = $request->get('filter', null);
            
            // Calculate date ranges based on filter
            if ($filter) {
                switch($filter) {
                    case 'today':
                        $filterStart = now()->startOfDay();
                        $filterEnd = now()->endOfDay();
                        break;
                    case 'yesterday':
                        $filterStart = now()->subDay()->startOfDay();
                        $filterEnd = now()->subDay()->endOfDay();
                        break;
                    case 'week':
                        $filterStart = now()->startOfWeek();
                        $filterEnd = now()->endOfWeek();
                        break;
                    case 'month':
                        $filterStart = now()->startOfMonth();
                        $filterEnd = now()->endOfMonth();
                        break;
                    case 'year':
                        $filterStart = now()->startOfYear();
                        $filterEnd = now()->endOfYear();
                        break;
                    default:
                        $filterStart = null;
                        $filterEnd = null;
                }
            } else {
                // Use custom dates if provided
                $filterStart = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
                $filterEnd = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;
            }
            
            $hasFilter = $filterStart && $filterEnd;
            
            // Filtered stats (based on provided date range)
            $filteredVisits = \App\VisitTracking::where('sales_rep_id', $staff->id);
            $filteredLeads = \App\Lead::where('created_by', $staff->id);
            $filteredOrders = Transaction::where('created_by', $staff->id)->whereIn('type', ['sell', 'sales_order']);
            
            if ($hasFilter) {
                $filteredVisits = $filteredVisits->whereBetween('start_time', [$filterStart, $filterEnd]);
                $filteredLeads = $filteredLeads->whereBetween('created_at', [$filterStart, $filterEnd]);
                $filteredOrders = $filteredOrders->whereBetween('created_at', [$filterStart, $filterEnd]);
            }
            
            $filteredVisitsCount = $filteredVisits->count();
            $filteredLeadsCount = $filteredLeads->count();
            $filteredOrdersCount = $filteredOrders->count();
            $filteredSalesSum = (clone $filteredOrders)->sum('final_total');
            
            // Fixed date ranges for reference
            $today = now()->startOfDay();
            $todayEnd = now()->endOfDay();
            $thisWeekStart = now()->startOfWeek();
            $thisMonthStart = now()->startOfMonth();
            $thisYearStart = now()->startOfYear();
            
            // Today's stats
            $todayVisits = \App\VisitTracking::where('sales_rep_id', $staff->id)
                ->whereBetween('start_time', [$today, $todayEnd])
                ->count();
            
            $todayLeads = \App\Lead::where('created_by', $staff->id)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->count();
            
            $todayOrders = Transaction::where('created_by', $staff->id)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->whereIn('type', ['sell', 'sales_order'])
                ->count();
            
            $todaySales = Transaction::where('created_by', $staff->id)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->whereIn('type', ['sell', 'sales_order'])
                ->sum('final_total');
            
            // This week's stats
            $weekVisits = \App\VisitTracking::where('sales_rep_id', $staff->id)
                ->where('start_time', '>=', $thisWeekStart)
                ->count();
            
            $weekLeads = \App\Lead::where('created_by', $staff->id)
                ->where('created_at', '>=', $thisWeekStart)
                ->count();
            
            $weekOrders = Transaction::where('created_by', $staff->id)
                ->where('created_at', '>=', $thisWeekStart)
                ->whereIn('type', ['sell', 'sales_order'])
                ->count();
            
            $weekSales = Transaction::where('created_by', $staff->id)
                ->where('created_at', '>=', $thisWeekStart)
                ->whereIn('type', ['sell', 'sales_order'])
                ->sum('final_total');
            
            // This month's stats
            $monthVisits = \App\VisitTracking::where('sales_rep_id', $staff->id)
                ->where('start_time', '>=', $thisMonthStart)
                ->count();
            
            $monthLeads = \App\Lead::where('created_by', $staff->id)
                ->where('created_at', '>=', $thisMonthStart)
                ->count();
            
            $monthOrders = Transaction::where('created_by', $staff->id)
                ->where('created_at', '>=', $thisMonthStart)
                ->whereIn('type', ['sell', 'sales_order'])
                ->count();
            
            $monthSales = Transaction::where('created_by', $staff->id)
                ->where('created_at', '>=', $thisMonthStart)
                ->whereIn('type', ['sell', 'sales_order'])
                ->sum('final_total');
            
            // This year's stats
            $yearVisits = \App\VisitTracking::where('sales_rep_id', $staff->id)
                ->where('start_time', '>=', $thisYearStart)
                ->count();
            
            $yearLeads = \App\Lead::where('created_by', $staff->id)
                ->where('created_at', '>=', $thisYearStart)
                ->count();
            
            $yearOrders = Transaction::where('created_by', $staff->id)
                ->where('created_at', '>=', $thisYearStart)
                ->whereIn('type', ['sell', 'sales_order'])
                ->count();
            
            $yearSales = Transaction::where('created_by', $staff->id)
                ->where('created_at', '>=', $thisYearStart)
                ->whereIn('type', ['sell', 'sales_order'])
                ->sum('final_total');
            
            // All time stats
            $totalVisits = \App\VisitTracking::where('sales_rep_id', $staff->id)->count();
            $totalLeads = \App\Lead::where('created_by', $staff->id)->count();
            $totalOrders = Transaction::where('created_by', $staff->id)
                ->whereIn('type', ['sell', 'sales_order'])
                ->count();
            $totalSales = Transaction::where('created_by', $staff->id)
                ->whereIn('type', ['sell', 'sales_order'])
                ->sum('final_total');
            
            // Lead status breakdown
            $leadsBreakdown = \App\Lead::where('created_by', $staff->id)
                ->select('lead_status', DB::raw('count(*) as count'))
                ->groupBy('lead_status')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->lead_status => $item->count];
                });
            
            // Recent activity
            $recentVisits = \App\VisitTracking::where('sales_rep_id', $staff->id)
                ->with(['lead:id,store_name,company_name'])
                ->orderBy('start_time', 'desc')
                ->limit(5)
                ->get()
                ->map(function($visit) {
                    $lead = $visit->lead;
                    return [
                        'id' => $visit->id,
                        'lead_name' => $lead ? ($lead->store_name ?? $lead->company_name ?? 'N/A') : 'N/A',
                        'status' => $visit->status,
                        'start_time' => $visit->start_time->format('Y-m-d H:i:s'),
                        'start_time_formatted' => $visit->start_time->format('M d, h:i A')
                    ];
                });
            
            return response()->json([
                'status' => true,
                'message' => 'Dashboard stats retrieved successfully',
                'has_filter' => $hasFilter,
                'date_range' => [
                    'start_date' => $filterStart ? $filterStart->format('Y-m-d') : null,
                    'end_date' => $filterEnd ? $filterEnd->format('Y-m-d') : null,
                    'start_datetime' => $filterStart ? $filterStart->format('Y-m-d H:i:s') : null,
                    'end_datetime' => $filterEnd ? $filterEnd->format('Y-m-d H:i:s') : null,
                ],
                'data' => [
                    'selected_period' => [
                        'visits' => $filteredVisitsCount,
                        'leads' => $filteredLeadsCount,
                        'orders' => $filteredOrdersCount,
                        'sales' => (float) $filteredSalesSum
                    ],
                    'today' => [
                        'visits' => $todayVisits,
                        'leads' => $todayLeads,
                        'orders' => $todayOrders,
                        'sales' => (float) $todaySales
                    ],
                    'this_week' => [
                        'visits' => $weekVisits,
                        'leads' => $weekLeads,
                        'orders' => $weekOrders,
                        'sales' => (float) $weekSales
                    ],
                    'this_month' => [
                        'visits' => $monthVisits,
                        'leads' => $monthLeads,
                        'orders' => $monthOrders,
                        'sales' => (float) $monthSales
                    ],
                    'this_year' => [
                        'visits' => $yearVisits,
                        'leads' => $yearLeads,
                        'orders' => $yearOrders,
                        'sales' => (float) $yearSales
                    ],
                    'all_time' => [
                        'visits' => $totalVisits,
                        'leads' => $totalLeads,
                        'orders' => $totalOrders,
                        'sales' => (float) $totalSales
                    ],
                    'leads_breakdown' => $leadsBreakdown,
                    'recent_visits' => $recentVisits
                ]
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, 
                'message' => 'Failed to retrieve dashboard stats', 
                'error' => $th->getMessage()
            ], 500);
        }
    }
    /**
     * Update FCM token for staff
     */
    public function updateFcmToken(Request $request)
    {
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            
            $request->validate([
                'fcm_token' => 'required|string'
            ]);
            
            $staff->update([
                'fcmToken' => $request->fcm_token
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'FCM token updated successfully'
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, 
                'message' => 'Failed to update FCM token', 
                'error' => $th->getMessage()
            ]);
        }
    }
    /**
     * Start shift (Go Online)
     */
    public function startShift(Request $request)
    {
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            
            // Validate request
            $request->validate([
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);
            
            // Check if there's already an active shift
            $activeShift = \App\SalesRepShift::where('sales_rep_id', $staff->id)
                ->where('status', 'active')
                ->whereNull('shift_end_time')
                ->first();
            
            if ($activeShift) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already have an active shift. Please end it first.',
                    'data' => [
                        'active_shift' => [
                            'id' => $activeShift->id,
                            'shift_start_time' => $activeShift->shift_start_time->format('Y-m-d H:i:s'),
                        ]
                    ]
                ], 400);
            }
            
            // Update user online status
            $staff->update([
                'is_online' => true,
                'last_online_at' => now()
            ]);
            
            // Create new shift record
            $shift = \App\SalesRepShift::create([
                'business_id' => $staff->business_id,
                'sales_rep_id' => $staff->id,
                'shift_start_time' => now(),
                'status' => 'active',
                'start_latitude' => $request->latitude,
                'start_longitude' => $request->longitude,
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Shift started successfully',
                'data' => [
                    'id' => $staff->id,
                    'is_online' => true,
                    'shift_start_time' => $shift->shift_start_time->format('Y-m-d H:i:s'),
                    'shift_id' => $shift->id
                ]
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to start shift',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * End shift (Go Offline)
     */
    public function endShift(Request $request)
    {
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            
            // Validate request
            $request->validate([
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'notes' => 'nullable|string|max:1000',
            ]);
            
            // Find active shift
            $activeShift = \App\SalesRepShift::where('sales_rep_id', $staff->id)
                ->where('status', 'active')
                ->whereNull('shift_end_time')
                ->first();
            
            if (!$activeShift) {
                return response()->json([
                    'status' => false,
                    'message' => 'No active shift found to end.',
                ], 400);
            }
            
            // Update shift with end time
            $activeShift->update([
                'shift_end_time' => now(),
                'status' => 'ended',
                'end_latitude' => $request->latitude,
                'end_longitude' => $request->longitude,
                'notes' => $request->notes,
            ]);
            
            // Calculate duration
            $activeShift->calculateDuration();
            
            // Refresh the model to get updated values
            $activeShift->refresh();
            
            // Update user online status
            $staff->update([
                'is_online' => false,
                'last_online_at' => now()
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Shift ended successfully',
                'data' => [
                    'id' => $staff->id,
                    'is_online' => false,
                    'shift_end_time' => $activeShift->shift_end_time->format('Y-m-d H:i:s'),
                    'shift_id' => $activeShift->id,
                    'duration_minutes' => $activeShift->duration_minutes,
                    'duration_formatted' => $activeShift->formatted_duration
                ]
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to end shift',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            
            // Automatically end any active shift when logging out
            $activeShift = \App\SalesRepShift::where('sales_rep_id', $staff->id)
                ->where('status', 'active')
                ->whereNull('shift_end_time')
                ->first();
            
            if ($activeShift) {
                $activeShift->update([
                    'shift_end_time' => now(),
                    'status' => 'ended',
                    'notes' => 'Automatically ended on logout'
                ]);
                $activeShift->calculateDuration();
                $activeShift->refresh();
            }
            
            // Update user online status
            $staff->update([
                'is_online' => false,
                'last_online_at' => now()
            ]);
            
            $token = $request->header('Authorization');
            $token = str_replace('Bearer ', '', $token);
            JWTAuth::invalidate($token);
            
            return response()->json(['status' => 'success', 'message' => 'User logged out successfully']);
        } catch (JWTException $exception) {
            return response()->json(['status' => 'error', 'message' => 'Could not log out the user'], 500);
        }
    }
    public function loggingActive(Request $request, $status)
    {
        try {
            // Authenticate the staff user
            $staff = JWTAuth::parseToken()->authenticate();
            
            // Validate the status parameter
            if (!in_array($status, ['true', 'false'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid status parameter. Must be true/false or 1/0'
                ], 400);
            }
            
            // Update or create the picker activity record
            $pickerActivity = PickersActivity::updateOrCreate(
                ['user_id' => $staff->id], // Find by user_id
                [
                    'is_active' => $status == 'true' ? true : false,
                ]
            );
            $verifierActivity = VerifierActivity::updateOrCreate(
                ['user_id' => $staff->id], // Find by user_id
                [
                    'is_active' => $status == 'true' ? true : false,
                ]
            );
            
            return response()->json([
                'status' => true,
                'message' => $status == 'true' ? 'Picker marked as active' : 'Picker marked as inactive',
                'data' => [
                    'user_id' => $staff->id,
                    'is_active' => $pickerActivity->is_active,
                    'is_active_verifier' => $verifierActivity->is_active
                ]
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update picker activity status',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function curruntStatus(Request $request){
        $staff = JWTAuth::parseToken()->authenticate();
        $activity = PickersActivity::where('user_id', $staff->id)->first();
        $verifierActivity = VerifierActivity::where('user_id', $staff->id)->first();
        return response()->json([
            'status' => true,
            'message' => 'Current status retrieved successfully',
            'current_status' => $activity->current_status,
            'is_active_verifier' => $verifierActivity->is_active
        ]);
    }
}
