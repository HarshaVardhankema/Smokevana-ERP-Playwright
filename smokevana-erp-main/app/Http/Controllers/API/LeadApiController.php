<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Lead;
use App\VisitTracking;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class LeadApiController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    /**
     * Get all leads with filters
     * Unified API that supports both list view and map view
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $business_id = $request->user()->business_id;

            $query = Lead::where('business_id', $business_id)
                ->with([
                    'assignedTo:id,first_name,last_name,email,contact_number', 
                    'createdByUser:id,first_name,last_name',
                    'salesRep:id,first_name,last_name,email,contact_number',
                    'visitor:id,first_name,last_name' // Load visited_by user
                ]);

            // MAP FILTER: Only leads with valid GPS coordinates
            if ($request->boolean('with_coordinates_only')) {
                $query->whereNotNull('latitude')
                      ->whereNotNull('longitude')
                      ->where('latitude', '!=', 0)
                      ->where('longitude', '!=', 0);
            }

            // Apply filters
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('lead_status') && $request->lead_status != '') {
                $query->where('lead_status', $request->lead_status);
            }

            if ($request->has('priority') && $request->priority != '') {
                $query->where('priority', $request->priority);
            }

            if ($request->has('assigned_to') && $request->assigned_to != '') {
                $query->where('assigned_to', $request->assigned_to);
            }

            // Filter by sales rep
            if ($request->has('sales_rep_id') && $request->sales_rep_id != '') {
                $query->where('sales_rep_id', $request->sales_rep_id);
            }

            if ($request->has('source') && $request->source != '') {
                $query->where('source', $request->source);
            }

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('store_name', 'like', '%' . $search . '%');
                    
                    if (\Schema::hasColumn('leads', 'contact_name')) {
                        $q->orWhere('contact_name', 'like', '%' . $search . '%');
                    }
                    
                    if (\Schema::hasColumn('leads', 'contact_phone')) {
                        $q->orWhere('contact_phone', 'like', '%' . $search . '%');
                    }
                    
                    if (\Schema::hasColumn('leads', 'reference_no')) {
                        $q->orWhere('reference_no', 'like', '%' . $search . '%');
                    }
                });
            }

            // MAP MODE: Return all without pagination
            if ($request->boolean('no_pagination')) {
                $leads = $query->orderBy('created_at', 'desc')->get();
                
                return response()->json([
                    'success' => true,
                    'data' => $this->formatLeadsForMap($leads),
                    'total' => $leads->count()
                ]);
            }

            // LIST MODE: Paginated results
            $per_page = $request->get('per_page', 20);
            $leads = $query->orderBy('created_at', 'desc')->paginate($per_page);

            return response()->json([
                'success' => true,
                'data' => $this->formatLeadsForMap(collect($leads->items())),
                'pagination' => [
                    'total' => $leads->total(),
                    'per_page' => $leads->perPage(),
                    'current_page' => $leads->currentPage(),
                    'last_page' => $leads->lastPage(),
                    'from' => $leads->firstItem(),
                    'to' => $leads->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format leads data optimized for map display
     * 
     * @param Collection $leads
     * @return Collection
     */
    private function formatLeadsForMap($leads)
    {
        $currentUserId = auth()->user()->id;
        
        return $leads->map(function($lead) use ($currentUserId) {
            $salesRep = null;
            if ($lead->salesRep) {
                $salesRep = [
                    'id' => $lead->salesRep->id,
                    'name' => trim($lead->salesRep->first_name . ' ' . $lead->salesRep->last_name),
                    'first_name' => $lead->salesRep->first_name,
                    'last_name' => $lead->salesRep->last_name,
                    'email' => $lead->salesRep->email,
                    'phone' => $lead->salesRep->contact_number
                ];
            }
            
            // Created by user
            $createdBy = null;
            if ($lead->createdByUser) {
                $createdBy = [
                    'id' => $lead->createdByUser->id,
                    'name' => trim($lead->createdByUser->first_name . ' ' . $lead->createdByUser->last_name),
                    'first_name' => $lead->createdByUser->first_name,
                    'last_name' => $lead->createdByUser->last_name,
                ];
            }
            
            // Visited by user
            $visitedBy = null;
            if ($lead->visitor) {
                $visitedBy = [
                    'id' => $lead->visitor->id,
                    'name' => trim($lead->visitor->first_name . ' ' . $lead->visitor->last_name),
                    'first_name' => $lead->visitor->first_name,
                    'last_name' => $lead->visitor->last_name,
                ];
            }
            
            // Get visit statistics for this lead
            $totalVisits = 0;
            $lastVisitDate = null;
            $visitStatus = 'never_visited';
            
            if (\Schema::hasTable('visit_tracking')) {
                $totalVisits = VisitTracking::where('lead_id', $lead->id)
                    ->where('status', 'completed')
                    ->count();
                
                $lastVisit = VisitTracking::where('lead_id', $lead->id)
                    ->where('status', 'completed')
                    ->orderBy('checkout_time', 'desc')
                    ->first();
                
                if ($lastVisit) {
                    $lastVisitDate = $lastVisit->checkout_time->format('M d, Y h:i A');
                    $visitStatus = 'visited';
                } else if ($lead->last_contact_date) {
                    $lastVisitDate = $lead->last_contact_date->format('M d, Y');
                    $visitStatus = 'visited';
                }
            }
            
            // ============================================
            // CHECK IF CURRENT USER CAN VISIT THIS LEAD
            // ============================================
            $canVisit = true;
            $cannotVisitReason = null;
            $assignedToOtherRep = false;
            
            // Check if lead has been VISITED (completed visit) by someone else
            // Only block if there's a completed visit by another sales rep
            if (\Schema::hasTable('visit_tracking')) {
                $firstCompletedVisit = VisitTracking::where('lead_id', $lead->id)
                    ->where('status', 'completed')
                    ->orderBy('checkout_time', 'asc')
                    ->first();
                
                if ($firstCompletedVisit && $firstCompletedVisit->sales_rep_id != $currentUserId) {
                    // This lead has been visited by another sales rep
                    $canVisit = false;
                    $assignedToOtherRep = true;
                    
                    // Get the assigned sales rep's name
                    $assignedSalesRep = \App\User::find($firstCompletedVisit->sales_rep_id);
                    $assignedRepName = $assignedSalesRep 
                        ? trim($assignedSalesRep->first_name . ' ' . $assignedSalesRep->last_name)
                        : 'Unknown';
                    
                    $cannotVisitReason = "Already visited by {$assignedRepName}";
                }
            }
            
            return [
                'id' => $lead->id,
                'reference_no' => $lead->reference_no ?? 'N/A',
                'store_name' => $lead->store_name,
                'company_name' => $lead->company_name,
                'contact_name' => $lead->contact_name,
                'contact_phone' => $lead->contact_phone,
                'contact_email' => $lead->contact_email ?? null,
                'full_address' => $lead->full_address ?? trim(implode(', ', array_filter([
                    $lead->address_line_1,
                    $lead->city,
                    $lead->state,
                    $lead->country
                ]))),
                'address_line_1' => $lead->address_line_1,
                'city' => $lead->city,
                'state' => $lead->state,
                'country' => $lead->country,
                'zip_code' => $lead->zip_code,
                'latitude' => (float) $lead->latitude,
                'longitude' => (float) $lead->longitude,
                
                // Status fields
                'status' => $lead->status ?? 'pending', // Visit status (pending/visited)
                'lead_status' => $lead->lead_status ?? 'new', // Lead status (new/qualified/etc)
                'visit_status' => $visitStatus, // 'visited' or 'never_visited'
                'priority' => $lead->priority ?? 'medium',
                'total_visits' => $totalVisits,
                'last_visit_date' => $lastVisitDate ?? 'Never',
                'last_contact_date' => $lead->last_contact_date ? $lead->last_contact_date->format('Y-m-d H:i:s') : null,
                
                // User relationships
                'sales_rep_id' => $lead->sales_rep_id,
                'sales_rep' => $salesRep,
                'sales_rep_name' => $salesRep ? $salesRep['name'] : 'Unassigned',
                'created_by' => $createdBy, // Created by user object
                'visited_by' => $visitedBy, // Visited by user object
                
                // ✨ NEW: Visit permission fields for mobile app
                'can_visit' => $canVisit, // true/false - Can current user visit this lead?
                'assigned_to_other_rep' => $assignedToOtherRep, // true/false - Is this assigned to someone else?
                'cannot_visit_reason' => $cannotVisitReason, // Reason message if can't visit
                
                'created_at' => $lead->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $lead->updated_at->format('Y-m-d H:i:s')
            ];
        });
    }

    /**
     * Get single lead details with ticket and visit statistics
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = auth()->user();
            $business_id = $user->business_id;

            // Get lead with essential relationships only
            $lead = Lead::where('business_id', $business_id)
                ->with([
                    'salesRep' => function($query) {
                        $query->select('id', 'first_name', 'last_name', 'email', 'contact_number');
                    },
                    'createdByUser' => function($query) {
                        $query->select('id', 'first_name', 'last_name');
                    },
                    'visitor' => function($query) {
                        $query->select('id', 'first_name', 'last_name');
                    }
                ])
                ->findOrFail($id);

            // Calculate visit statistics (only if table exists)
            $visitStats = [
                'total_visits' => 0,
                'completed_visits' => 0,
                'pending_visits' => 0,
                'in_progress_visits' => 0,
                'scheduled_visits' => 0,
                'cancelled_visits' => 0,
            ];
            
            $lastVisit = null;
            
            if (\Schema::hasTable('visit_tracking')) {
                $visitStats = [
                    'total_visits' => VisitTracking::where('lead_id', $id)->count(),
                    'completed_visits' => VisitTracking::where('lead_id', $id)->where('status', 'completed')->count(),
                    'pending_visits' => VisitTracking::where('lead_id', $id)->where('status', 'pending')->count(),
                    'in_progress_visits' => VisitTracking::where('lead_id', $id)->where('status', 'in_progress')->count(),
                ];

                // Get last visit details
                $lastVisit = VisitTracking::where('lead_id', $id)
                    ->orderBy('start_time', 'desc')
                    ->select('id', 'start_time', 'duration', 'status', 'visit_type')
                    ->first();
            }

            // Calculate ticket statistics (only if table exists)
            $ticketStats = [
                'total_tickets' => 0,
                'open_tickets' => 0,
                'in_progress_tickets' => 0,
                'pending_tickets' => 0,
                'resolved_tickets' => 0,
                'closed_tickets' => 0,
            ];
            
            $allTickets = collect([]);
            
            if (\Schema::hasTable('tickets')) {
                $ticketStats = [
                    'total_tickets' => \App\Ticket::where('lead_id', $id)->count(),
                    'open_tickets' => \App\Ticket::where('lead_id', $id)->where('status', 'open')->count(),
                    'in_progress_tickets' => \App\Ticket::where('lead_id', $id)->where('status', 'in_progress')->count(),
                    'pending_tickets' => \App\Ticket::where('lead_id', $id)->where('status', 'pending')->count(),
                    'resolved_tickets' => \App\Ticket::where('lead_id', $id)->where('status', 'resolved')->count(),
                    'closed_tickets' => \App\Ticket::where('lead_id', $id)->where('status', 'closed')->count(),
                ];

                // Get ALL tickets for this lead (not limited)
                // Check if columns exist for backward compatibility
                $ticketColumns = ['id', 'reference_no', 'ticket_description', 'status', 'user_id', 'created_at', 'updated_at'];
                
                // Add optional columns if they exist
                if (\Schema::hasColumn('tickets', 'issue_type')) {
                    $ticketColumns[] = 'issue_type';
                }
                if (\Schema::hasColumn('tickets', 'issue_priority')) {
                    $ticketColumns[] = 'issue_priority';
                }
                if (\Schema::hasColumn('tickets', 'initial_image')) {
                    $ticketColumns[] = 'initial_image';
                }
                if (\Schema::hasColumn('tickets', 'closed_by')) {
                    $ticketColumns[] = 'closed_by';
                }
                if (\Schema::hasColumn('tickets', 'closed_at')) {
                    $ticketColumns[] = 'closed_at';
                }
                
                $allTickets = \App\Ticket::where('lead_id', $id)
                    ->with(['user:id,first_name,last_name', 'closedBy:id,first_name,last_name'])
                    ->orderBy('created_at', 'desc')
                    ->select($ticketColumns)
                    ->get()
                    ->map(function($ticket) {
                        $data = [
                            'id' => $ticket->id,
                            'ticket_description' => $ticket->ticket_description,
                            'status' => $ticket->status,
                            'created_by' => $ticket->user ? [
                                'id' => $ticket->user->id,
                                'name' => trim($ticket->user->first_name . ' ' . $ticket->user->last_name),
                                'first_name' => $ticket->user->first_name,
                                'last_name' => $ticket->user->last_name,
                            ] : null,
                            'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                            'created_at_human' => $ticket->created_at->diffForHumans(),
                            'updated_at' => $ticket->updated_at->format('Y-m-d H:i:s'),
                        ];
                        
                        // Add reference_no if exists
                        if (isset($ticket->reference_no)) {
                            $data['reference_no'] = $ticket->reference_no;
                        }
                        
                        // Add optional fields if they exist
                        if (isset($ticket->issue_type)) {
                            $data['issue_type'] = $ticket->issue_type;
                        }
                        if (isset($ticket->issue_priority)) {
                            $data['issue_priority'] = $ticket->issue_priority ?? 'medium';
                        }
                        
                        // Always include image fields (null if not set)
                        if (isset($ticket->initial_image)) {
                            $data['has_image'] = !empty($ticket->initial_image);
                            $data['initial_image'] = $ticket->initial_image;
                            $data['initial_image_url'] = $ticket->initial_image ? url('uploads/tickets/' . $ticket->initial_image) : null;
                            $data['image_full_url'] = $ticket->initial_image ? url('uploads/tickets/' . $ticket->initial_image) : null;
                        } else {
                            $data['has_image'] = false;
                            $data['initial_image'] = null;
                            $data['initial_image_url'] = null;
                        }
                        
                        if (isset($ticket->closed_by)) {
                            $data['closed_by'] = $ticket->closedBy ? [
                                'id' => $ticket->closedBy->id,
                                'name' => trim($ticket->closedBy->first_name . ' ' . $ticket->closedBy->last_name),
                                'first_name' => $ticket->closedBy->first_name,
                                'last_name' => $ticket->closedBy->last_name,
                            ] : null;
                        }
                        if (isset($ticket->closed_at)) {
                            $data['closed_at'] = $ticket->closed_at ? $ticket->closed_at->format('Y-m-d H:i:s') : null;
                        }
                        
                        return $data;
                    });
            }

            // Format sales rep data
            $salesRepData = null;
            if ($lead->salesRep) {
                $salesRepData = [
                    'id' => $lead->salesRep->id,
                    'name' => trim($lead->salesRep->first_name . ' ' . $lead->salesRep->last_name),
                    'first_name' => $lead->salesRep->first_name,
                    'last_name' => $lead->salesRep->last_name,
                    'email' => $lead->salesRep->email,
                    'phone' => $lead->salesRep->contact_number,
                ];
            }

            // Format created by data
            $createdByData = null;
            if ($lead->createdByUser) {
                $createdByData = [
                    'id' => $lead->createdByUser->id,
                    'name' => trim($lead->createdByUser->first_name . ' ' . $lead->createdByUser->last_name),
                    'first_name' => $lead->createdByUser->first_name,
                    'last_name' => $lead->createdByUser->last_name,
                ];
            }

            // Format visited by data
            $visitedByData = null;
            if ($lead->visitor) {
                $visitedByData = [
                    'id' => $lead->visitor->id,
                    'name' => trim($lead->visitor->first_name . ' ' . $lead->visitor->last_name),
                    'first_name' => $lead->visitor->first_name,
                    'last_name' => $lead->visitor->last_name,
                ];
            }

            // Determine visit status
            $visitStatus = 'never_visited';
            if ($visitStats['completed_visits'] > 0 || $lead->last_contact_date) {
                $visitStatus = 'visited';
            }
            
            // ============================================
            // CHECK IF CURRENT USER CAN VISIT THIS LEAD
            // ============================================
            $canVisit = true;
            $cannotVisitReason = null;
            $assignedToOtherRep = false;
            
            // Check if lead has been VISITED (completed visit) by someone else
            // Only block if there's a completed visit by another sales rep
            $firstCompletedVisit = VisitTracking::where('lead_id', $lead->id)
                ->where('status', 'completed')
                ->orderBy('checkout_time', 'asc')
                ->first();
            
            if ($firstCompletedVisit && $firstCompletedVisit->sales_rep_id != $user->id) {
                // This lead has been visited by another sales rep
                $canVisit = false;
                $assignedToOtherRep = true;
                
                // Get the assigned sales rep's name
                $assignedSalesRep = \App\User::find($firstCompletedVisit->sales_rep_id);
                $assignedRepName = $assignedSalesRep 
                    ? trim($assignedSalesRep->first_name . ' ' . $assignedSalesRep->last_name)
                    : 'Unknown';
                
                $cannotVisitReason = "Already visited by {$assignedRepName}";
            }
            
            // Build clean response
            $response = [
                // Lead basic information
                'id' => $lead->id,
                'reference_no' => $lead->reference_no,
                'store_name' => $lead->store_name,
                'company_name' => $lead->company_name,
                'contact_name' => $lead->contact_name,
                'contact_phone' => $lead->contact_phone,
                'contact_email' => $lead->contact_email,
                
                // Address information
                'address_line_1' => $lead->address_line_1,
                'address_line_2' => $lead->address_line_2,
                'city' => $lead->city,
                'state' => $lead->state,
                'country' => $lead->country,
                'zip_code' => $lead->zip_code,
                'full_address' => $lead->full_address,
                'latitude' => $lead->latitude ? (float) $lead->latitude : null,
                'longitude' => $lead->longitude ? (float) $lead->longitude : null,
                
                // Lead status and priority
                'status' => $lead->status,
                'lead_status' => $lead->lead_status,
                'visit_status' => $visitStatus, // 'visited' or 'never_visited'
                'priority' => $lead->priority,
                'lead_source' => $lead->lead_source,
                
                // Dates
                'last_contact_date' => $lead->last_contact_date ? $lead->last_contact_date->format('Y-m-d H:i:s') : null,
                'next_follow_up_date' => $lead->next_follow_up_date ? $lead->next_follow_up_date->format('Y-m-d') : null,
                'created_at' => $lead->created_at->format('Y-m-d H:i:s'),
                
                // Notes
                'notes' => $lead->notes,
                
                // Sales representative
                'sales_rep_id' => $lead->sales_rep_id,
                'sales_rep' => $salesRepData,
                
                // Created by user
                'created_by' => $createdByData,
                
                // Visited by user
                'visited_by' => $visitedByData,
                
                // ✨ NEW: Visit permission fields
                'can_visit' => $canVisit,
                'assigned_to_other_rep' => $assignedToOtherRep,
                'cannot_visit_reason' => $cannotVisitReason,
                
                // Visit statistics
                'visit_statistics' => $visitStats,
                'last_visit' => $lastVisit,
                
                // Ticket statistics and ALL tickets
                'ticket_statistics' => $ticketStats,
                'tickets' => $allTickets,
            ];

            return response()->json([
                'success' => true,
                'data' => $response
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found or error occurred: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create new lead
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $business_id = $user->business_id;

            // ============================================
            // LOG: Request Data Received from Mobile App
            // ============================================
            \Log::info('=== CREATE LEAD REQUEST RECEIVED ===');
            \Log::info('User ID: ' . $user->id);
            \Log::info('User Name: ' . $user->first_name . ' ' . $user->last_name);
            \Log::info('Request All Data: ', $request->all());
            \Log::info('Latitude from Request: ' . ($request->latitude ?? 'NOT PROVIDED'));
            \Log::info('Longitude from Request: ' . ($request->longitude ?? 'NOT PROVIDED'));
            \Log::info('Address from Request: ' . ($request->address ?? ($request->address_line_1 ?? 'NOT PROVIDED')));
            \Log::info('=====================================');

            // Build validation rules dynamically based on schema
            $rules = [
                'store_name' => 'required|string|max:255',
                'contact_name' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'address_line_1' => 'nullable|string',
                'address_line_2' => 'nullable|string',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:20',
                'full_address' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'priority' => 'nullable|in:low,medium,high,urgent',
                'source' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
            ];

            // Only validate contact_email if column exists
            if (\Schema::hasColumn('leads', 'contact_email')) {
                $rules['contact_email'] = 'nullable|email|max:255';
            }
            
            // Only validate contact_phone if column exists
            if (\Schema::hasColumn('leads', 'contact_phone')) {
                $rules['contact_phone'] = 'nullable|string|min:10|max:10';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate reference number
            $lastLead = Lead::where('business_id', $business_id)->latest('id')->first();
            $nextNumber = $lastLead ? intval(substr($lastLead->reference_no, -5)) + 1 : 1;
            $referenceNo = 'LEAD-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Handle address fields - support both 'address' and 'address_line_1'
            $address_line_1 = $request->address_line_1 ?? $request->address ?? '';
            $city = $request->city ?? '';
            $state = $request->state ?? '';
            $country = $request->country ?? '';
            $zip_code = $request->zip_code ?? '';
            $address_line_2 = $request->address_line_2 ?? '';

            // Build full_address from components if not provided
            $full_address = $request->full_address;
            if (empty($full_address)) {
                $addressComponents = array_filter([
                    $address_line_1,
                    $address_line_2,
                    $city,
                    $state,
                    $country,
                    $zip_code
                ]);
                $full_address = implode(', ', $addressComponents);
            }

            // Auto-geocode address if latitude/longitude not provided or invalid (0,0)
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            // Convert to float for proper validation
            $lat = $latitude ? (float) $latitude : 0;
            $lng = $longitude ? (float) $longitude : 0;
            
            // Treat 0,0 coordinates as invalid and trigger geocoding
            $needsGeocoding = ($lat == 0 && $lng == 0) || empty($latitude) || empty($longitude);
            
            if ($needsGeocoding && !empty($full_address)) {
                \Log::info('Geocoding needed - Latitude: ' . $latitude . ', Longitude: ' . $longitude);
                \Log::info('Full address for geocoding: ' . $full_address);
                
                $geocodeResult = $this->geocodingService->geocodeFromComponents([
                    'address_line_1' => $address_line_1,
                    'address_line_2' => $address_line_2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'zip_code' => $zip_code
                ]);
                
                if ($geocodeResult) {
                    $latitude = $geocodeResult['latitude'];
                    $longitude = $geocodeResult['longitude'];
                    \Log::info('Geocoding successful - New Latitude: ' . $latitude . ', Longitude: ' . $longitude);
                    
                    // Optionally update full_address with Google's formatted address
                    if (isset($geocodeResult['formatted_address'])) {
                        $full_address = $geocodeResult['formatted_address'];
                    }
                } else {
                    \Log::warning('Geocoding failed for address: ' . $full_address);
                }
            } else {
                \Log::info('Using provided coordinates - Latitude: ' . $latitude . ', Longitude: ' . $longitude);
            }

            // Build lead data dynamically based on schema
            $leadData = [
                'business_id' => $business_id,
                'reference_no' => $referenceNo,
                'store_name' => $request->store_name,
                'address_line_1' => $address_line_1,
                'address_line_2' => $address_line_2,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'zip_code' => $zip_code,
                'full_address' => $full_address,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'status' => 'pending', // Visit status: pending (not yet visited)
                'created_by' => $user->id,
            ];

            // Add optional fields if columns exist
            if (\Schema::hasColumn('leads', 'contact_name')) {
                $leadData['contact_name'] = $request->contact_name;
            }
            if (\Schema::hasColumn('leads', 'contact_phone')) {
                $leadData['contact_phone'] = $request->contact_phone;
            }
            if (\Schema::hasColumn('leads', 'contact_email')) {
                $leadData['contact_email'] = $request->contact_email;
            }
            if (\Schema::hasColumn('leads', 'lead_status')) {
                $leadData['lead_status'] = $request->status ?? 'new'; // Lead status: new lead
            }
            if (\Schema::hasColumn('leads', 'priority')) {
                $leadData['priority'] = $request->priority ?? 'medium';
            }
            if (\Schema::hasColumn('leads', 'lead_source')) {
                $leadData['lead_source'] = $request->lead_source ?? $request->source ?? 'mobile_app';
            }
            if (\Schema::hasColumn('leads', 'assigned_to')) {
                $leadData['assigned_to'] = $request->assigned_to ?? $user->id;
            }
            if (\Schema::hasColumn('leads', 'sales_rep_id')) {
                $leadData['sales_rep_id'] = $request->sales_rep_id ?? $request->assigned_to ?? $user->id;
            }
            if (\Schema::hasColumn('leads', 'notes')) {
                $leadData['notes'] = $request->notes;
            }
            if (\Schema::hasColumn('leads', 'funnel_stage')) {
                $leadData['funnel_stage'] = $request->funnel_stage ?? 'initial_contact';
            }
            if (\Schema::hasColumn('leads', 'currency')) {
                $leadData['currency'] = $request->currency ?? 'USD';
            }
            if (\Schema::hasColumn('leads', 'preferred_contact_method')) {
                $leadData['preferred_contact_method'] = $request->preferred_contact_method ?? 'phone';
            }
            if (\Schema::hasColumn('leads', 'is_hot_lead')) {
                $leadData['is_hot_lead'] = $request->boolean('is_hot_lead', false);
            }
            if (\Schema::hasColumn('leads', 'requires_immediate_attention')) {
                $leadData['requires_immediate_attention'] = $request->boolean('requires_immediate_attention', false);
            }
            if (\Schema::hasColumn('leads', 'reference_no')) {
                $leadData['reference_no'] = $request->reference_no ?? $referenceNo;
            }

            // ============================================
            // CONSOLE LOG: Final Data Before Insert
            // ============================================
            \Log::info('=== FINAL LEAD DATA TO BE INSERTED ===');
            \Log::info('Lead Data Array: ', $leadData);
            \Log::info('Lead Data JSON: ' . json_encode($leadData, JSON_PRETTY_PRINT));
            \Log::info('Reference Number: ' . ($leadData['reference_no'] ?? 'NOT SET'));
            \Log::info('Has Latitude: ' . (isset($leadData['latitude']) ? 'YES (' . $leadData['latitude'] . ')' : 'NO'));
            \Log::info('Has Longitude: ' . (isset($leadData['longitude']) ? 'YES (' . $leadData['longitude'] . ')' : 'NO'));
            
            // Check if coordinates are still 0,0 after processing
            $finalLat = isset($leadData['latitude']) ? (float) $leadData['latitude'] : 0;
            $finalLng = isset($leadData['longitude']) ? (float) $longitude : 0;
            if ($finalLat == 0 && $finalLng == 0) {
                \Log::warning('⚠️ WARNING: Lead will be created with 0,0 coordinates!');
                \Log::warning('Original Request Latitude: ' . $request->latitude);
                \Log::warning('Original Request Longitude: ' . $request->longitude);
                \Log::warning('Full Address: ' . $full_address);
            }
            \Log::info('======================================');

            $lead = Lead::create($leadData);

            // ============================================
            // CONSOLE LOG: Lead Created Successfully
            // ============================================
            \Log::info('=== LEAD CREATED SUCCESSFULLY ===');
            \Log::info('Lead ID: ' . $lead->id);
            \Log::info('Reference No: ' . ($lead->reference_no ?? 'NULL'));
            \Log::info('Store Name: ' . $lead->store_name);
            \Log::info('Created Lead Full Data: ', $lead->toArray());
            \Log::info('Latitude in Response: ' . ($lead->latitude ?? 'NULL'));
            \Log::info('Longitude in Response: ' . ($lead->longitude ?? 'NULL'));
            \Log::info('==================================');

            // Refresh the lead to ensure all data is loaded
            $lead->refresh();

            // Format response data properly
            $responseData = [
                'id' => $lead->id,
                'reference_no' => $lead->reference_no,
                'store_name' => $lead->store_name,
                'company_name' => $lead->company_name ?? null,
                'contact_name' => $lead->contact_name ?? null,
                'contact_phone' => $lead->contact_phone ?? null,
                'contact_email' => $lead->contact_email ?? null,
                'address_line_1' => $lead->address_line_1,
                'address_line_2' => $lead->address_line_2,
                'city' => $lead->city,
                'state' => $lead->state,
                'country' => $lead->country,
                'zip_code' => $lead->zip_code,
                'full_address' => $lead->full_address,
                'latitude' => $lead->latitude ? (float) $lead->latitude : null,
                'longitude' => $lead->longitude ? (float) $lead->longitude : null,
                'status' => $lead->status ?? 'pending',
                'lead_status' => $lead->lead_status ?? 'new',
                'priority' => $lead->priority ?? 'medium',
                'lead_source' => $lead->lead_source ?? 'mobile_app',
                'notes' => $lead->notes ?? null,
                'created_at' => $lead->created_at->toDateTimeString(),
                'updated_at' => $lead->updated_at->toDateTimeString(),
            ];

            // Check if coordinates are 0,0 and add warning
            $warning = null;
            $responseLat = $responseData['latitude'] ?? 0;
            $responseLng = $responseData['longitude'] ?? 0;
            
            if ((float)$responseLat == 0 && (float)$responseLng == 0) {
                $warning = 'Lead created but GPS coordinates are 0,0. Please ensure mobile app is sending valid latitude/longitude or provide complete address for geocoding.';
                \Log::warning($warning);
            }
            
            $response = [
                'success' => true,
                'message' => 'Lead created successfully',
                'data' => $responseData
            ];
            
            if ($warning) {
                $response['warning'] = $warning;
            }
            
            return response()->json($response, 201);

        } catch (\Exception $e) {
            // ============================================
            // CONSOLE LOG: Lead Creation Failed
            // ============================================
            \Log::error('=== LEAD CREATION FAILED ===');
            \Log::error('Error Message: ' . $e->getMessage());
            \Log::error('Error File: ' . $e->getFile());
            \Log::error('Error Line: ' . $e->getLine());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            \Log::error('Request Payload: ', $request->all());
            \Log::error('================================');
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update lead
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $business_id = $user->business_id;

            $lead = Lead::where('business_id', $business_id)->findOrFail($id);

            // Build validation rules dynamically based on schema
            $rules = [
                'store_name' => 'sometimes|required|string|max:255',
                'address' => 'nullable|string',
                'address_line_1' => 'nullable|string',
                'address_line_2' => 'nullable|string',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:20',
                'full_address' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'status' => 'nullable|in:pending,visited',
            ];

            // Only validate contact_name if column exists
            if (\Schema::hasColumn('leads', 'contact_name')) {
                $rules['contact_name'] = 'sometimes|required|string|max:255';
            }

            // Only validate contact_phone if column exists
            if (\Schema::hasColumn('leads', 'contact_phone')) {
                $rules['contact_phone'] = 'sometimes|nullable|string|min:10|max:10';
            }

            // Only validate contact_email if column exists
            if (\Schema::hasColumn('leads', 'contact_email')) {
                $rules['contact_email'] = 'nullable|email|max:255';
            }

            // Only validate lead_status if column exists
            if (\Schema::hasColumn('leads', 'lead_status')) {
                $rules['lead_status'] = 'nullable|in:new,in_progress,follow_up,converted,lost,qualified,unqualified';
            }

            // Only validate priority if column exists
            if (\Schema::hasColumn('leads', 'priority')) {
                $rules['priority'] = 'nullable|in:low,medium,high,urgent';
            }

            // Only validate source if column exists
            if (\Schema::hasColumn('leads', 'lead_source')) {
                $rules['source'] = 'nullable|string';
            }

            // Only validate assigned_to if column exists
            if (\Schema::hasColumn('leads', 'assigned_to')) {
                $rules['assigned_to'] = 'nullable|exists:users,id';
            }

            // Only validate sales_rep_id if column exists
            if (\Schema::hasColumn('leads', 'sales_rep_id')) {
                $rules['sales_rep_id'] = 'nullable|exists:users,id';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prepare update data
            $updateData = $request->only([
                'store_name', 'contact_name', 'contact_phone', 'contact_email',
                'latitude', 'longitude', 'status', 'lead_status', 'priority',
                'source', 'assigned_to', 'sales_rep_id', 'notes'
            ]);

            // Handle address fields - support both 'address' and 'address_line_1'
            if ($request->has('address') || $request->has('address_line_1')) {
                $updateData['address_line_1'] = $request->address_line_1 ?? $request->address ?? $lead->address_line_1;
            }
            if ($request->has('address_line_2')) {
                $updateData['address_line_2'] = $request->address_line_2;
            }
            if ($request->has('city')) {
                $updateData['city'] = $request->city;
            }
            if ($request->has('state')) {
                $updateData['state'] = $request->state;
            }
            if ($request->has('country')) {
                $updateData['country'] = $request->country;
            }
            if ($request->has('zip_code')) {
                $updateData['zip_code'] = $request->zip_code;
            }

            // Build full_address from components if not explicitly provided
            $addressChanged = false;
            if ($request->has('full_address')) {
                $updateData['full_address'] = $request->full_address;
                $addressChanged = true;
            } else if ($request->has(['address', 'city', 'state']) || 
                       $request->has(['address_line_1', 'city', 'state'])) {
                // Rebuild full_address from the updated components
                $addressComponents = array_filter([
                    $updateData['address_line_1'] ?? $lead->address_line_1,
                    $updateData['address_line_2'] ?? $lead->address_line_2,
                    $updateData['city'] ?? $lead->city,
                    $updateData['state'] ?? $lead->state,
                    $updateData['country'] ?? $lead->country,
                    $updateData['zip_code'] ?? $lead->zip_code
                ]);
                $updateData['full_address'] = implode(', ', $addressComponents);
                $addressChanged = true;
            }

            // Auto-geocode if address changed and lat/lng not explicitly provided
            if ($addressChanged && !$request->has('latitude') && !$request->has('longitude')) {
                $geocodeComponents = [
                    'address_line_1' => $updateData['address_line_1'] ?? $lead->address_line_1,
                    'address_line_2' => $updateData['address_line_2'] ?? $lead->address_line_2,
                    'city' => $updateData['city'] ?? $lead->city,
                    'state' => $updateData['state'] ?? $lead->state,
                    'country' => $updateData['country'] ?? $lead->country,
                    'zip_code' => $updateData['zip_code'] ?? $lead->zip_code
                ];
                
                $geocodeResult = $this->geocodingService->geocodeFromComponents($geocodeComponents);
                
                if ($geocodeResult) {
                    $updateData['latitude'] = $geocodeResult['latitude'];
                    $updateData['longitude'] = $geocodeResult['longitude'];
                    // Optionally update with Google's formatted address
                    if (isset($geocodeResult['formatted_address'])) {
                        $updateData['full_address'] = $geocodeResult['formatted_address'];
                    }
                }
            }

            $lead->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully',
                'data' => $lead->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete lead
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $business_id = $user->business_id;

            $lead = Lead::where('business_id', $business_id)->findOrFail($id);
            
            // Soft delete
            $lead->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lead deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test geocoding endpoint (for testing only)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testGeocode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->geocodingService->getCoordinates($request->address);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Geocoding successful',
                'data' => $result
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to geocode address. Check logs for details.',
            'data' => null
        ], 400);
    }

    /**
     * Get coordinates from Google Places place_id
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function geocodeFromPlaceId(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'place_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = $this->geocodingService->getCoordinatesFromPlaceId($request->place_id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully retrieved coordinates from place_id',
                    'data' => $result
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to get coordinates from place_id. Check logs for details.',
                'data' => null
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing place_id: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get visit history for a lead
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function visitHistory($id)
    {
        try {
            $user = auth()->user();
            $business_id = $user->business_id;

            $lead = Lead::where('business_id', $business_id)
                ->select('id', 'reference_no', 'store_name', 'contact_name', 'contact_phone', 
                        'address_line_1', 'city', 'state', 'status', 'lead_status')
                ->findOrFail($id);

            $visits = VisitTracking::where('lead_id', $id)
                ->with([
                    'salesRep:id,first_name,last_name,email,contact_number',
                    'createdBy:id,first_name,last_name'
                ])
                ->orderBy('start_time', 'desc')
                ->get();

            // Format visit data with complete information
            $formattedVisits = $visits->map(function($visit) {
                // Parse photo paths and create URLs
                $photoPaths = [];
                $photoUrls = [];
                if (!empty($visit->photo_proof_paths)) {
                    $photoPaths = json_decode($visit->photo_proof_paths, true);
                    if (is_array($photoPaths)) {
                        $photoUrls = array_map(function($path) {
                            return url('uploads/visits/' . $path);
                        }, $photoPaths);
                    }
                }
                
                return [
                    'id' => $visit->id,
                    'visit_date' => $visit->start_time ? $visit->start_time->format('Y-m-d') : null,
                    'visit_time' => $visit->start_time ? $visit->start_time->format('H:i:s') : null,
                    'visit_datetime' => $visit->start_time ? $visit->start_time->format('Y-m-d H:i:s') : null,
                    'duration' => $visit->duration,
                    'duration_formatted' => $visit->duration ? $visit->duration . ' minutes' : 'N/A',
                    'status' => $visit->status,
                    'visit_type' => $visit->visit_type,
                    
                    // Sales Rep who made the visit
                    'sales_rep_id' => $visit->sales_rep_id,
                    'sales_rep' => $visit->salesRep ? [
                        'id' => $visit->salesRep->id,
                        'name' => trim($visit->salesRep->first_name . ' ' . $visit->salesRep->last_name),
                        'first_name' => $visit->salesRep->first_name,
                        'last_name' => $visit->salesRep->last_name,
                        'email' => $visit->salesRep->email,
                        'phone' => $visit->salesRep->contact_number
                    ] : null,
                    'sales_rep_name' => $visit->salesRep ? 
                        trim($visit->salesRep->first_name . ' ' . $visit->salesRep->last_name) : 
                        'Unknown',
                    
                    // Visit proofs
                    'has_location_proof' => $visit->location_proof ?? false,
                    'has_photo_proof' => $visit->photo_proof ?? false,
                    'has_signature_proof' => $visit->signature_proof ?? false,
                    'has_video_proof' => $visit->video_proof ?? false,
                    'location_proof_path' => $visit->location_proof_path,
                    'photo_proof_paths' => $visit->photo_proof_paths,
                    'photo_urls' => $photoUrls, // ✅ ADDED: Array of full photo URLs
                    'photos_count' => count($photoUrls), // ✅ ADDED: Photo count
                    'signature_proof_path' => $visit->signature_proof_path,
                    'video_proof_path' => $visit->video_proof_path,
                    
                    // Additional info
                    'remarks' => $visit->remarks,
                    'created_at' => $visit->created_at ? $visit->created_at->format('Y-m-d H:i:s') : null,
                    'created_by' => $visit->createdBy ? [
                        'id' => $visit->createdBy->id,
                        'name' => trim($visit->createdBy->first_name . ' ' . $visit->createdBy->last_name)
                    ] : null
                ];
            });

            // Calculate statistics
            $completedVisits = $visits->where('status', 'completed')->count();
            $pendingVisits = $visits->where('status', 'pending')->count();
            $totalDuration = $visits->where('status', 'completed')->sum('duration');
            
            // Get unique sales reps who visited
            $uniqueSalesReps = $visits->filter(function($visit) {
                return $visit->salesRep !== null;
            })->map(function($visit) {
                return [
                    'id' => $visit->salesRep->id,
                    'name' => trim($visit->salesRep->first_name . ' ' . $visit->salesRep->last_name),
                    'visit_count' => 1
                ];
            })->groupBy('id')->map(function($group) {
                $first = $group->first();
                return [
                    'id' => $first['id'],
                    'name' => $first['name'],
                    'visit_count' => $group->count()
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $formattedVisits
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching visit history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all recent visits with lead data
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRecentVisits(Request $request)
    {
        try {
            $user = auth()->user();
            $business_id = $user->business_id;

            // Build query
            $query = VisitTracking::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->with([
                    'lead:id,reference_no,store_name,company_name,contact_name,contact_phone,city,state,full_address,latitude,longitude',
                    'salesRep:id,first_name,last_name,email,contact_number',
                    'createdBy:id,first_name,last_name'
                ]);

            // Filter by sales rep
            // If sales_rep_id is provided, use it (for admin/manager viewing other reps)
            // Otherwise, default to current user's visits only
            $sales_rep_id = $request->get('sales_rep_id', $user->id);
            if ($sales_rep_id != '' && $sales_rep_id != 'all') {
                $query->where('sales_rep_id', $sales_rep_id);
            }

            // Filter by status if provided
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('start_time', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('start_time', '<=', $request->date_to);
            }

            // Limit results (default 50, max 500)
            $limit = $request->get('limit', 50);
            if ($limit > 500) $limit = 500;

            $visits = $query->orderBy('start_time', 'desc')
                ->limit($limit)
                ->get();

            // Format visit data
            $formattedVisits = $visits->map(function($visit) {
                // Parse photo paths and create URLs
                $photoUrls = [];
                if (!empty($visit->photo_proof_paths)) {
                    $photoPaths = json_decode($visit->photo_proof_paths, true);
                    if (is_array($photoPaths)) {
                        $photoUrls = array_map(function($path) {
                            return url('uploads/visits/' . $path);
                        }, $photoPaths);
                    }
                }
                
                return [
                    'id' => $visit->id,
                    'visit_date' => $visit->start_time ? $visit->start_time->format('Y-m-d') : null,
                    'visit_time' => $visit->start_time ? $visit->start_time->format('H:i:s') : null,
                    'visit_datetime' => $visit->start_time ? $visit->start_time->format('Y-m-d H:i:s') : null,
                    'checkout_datetime' => $visit->checkout_time ? $visit->checkout_time->format('Y-m-d H:i:s') : null,
                    'duration' => $visit->duration,
                    'duration_formatted' => $visit->duration ? $visit->duration . ' minutes' : 'N/A',
                    'status' => $visit->status,
                    'visit_type' => $visit->visit_type,
                    
                    // Lead information
                    'lead_id' => $visit->lead_id,
                    'lead' => $visit->lead ? [
                        'id' => $visit->lead->id,
                        'reference_no' => $visit->lead->reference_no,
                        'store_name' => $visit->lead->store_name,
                        'company_name' => $visit->lead->company_name,
                        'contact_name' => $visit->lead->contact_name,
                        'contact_phone' => $visit->lead->contact_phone,
                        'city' => $visit->lead->city,
                        'state' => $visit->lead->state,
                        'full_address' => $visit->lead->full_address,
                        'latitude' => $visit->lead->latitude ? (float) $visit->lead->latitude : null,
                        'longitude' => $visit->lead->longitude ? (float) $visit->lead->longitude : null
                    ] : null,
                    'store_name' => $visit->lead->store_name ?? 'Unknown',
                    'lead_address' => $visit->lead->full_address ?? 'N/A',
                    
                    // Sales Rep who made the visit
                    'sales_rep_id' => $visit->sales_rep_id,
                    'sales_rep' => $visit->salesRep ? [
                        'id' => $visit->salesRep->id,
                        'name' => trim($visit->salesRep->first_name . ' ' . $visit->salesRep->last_name),
                        'first_name' => $visit->salesRep->first_name,
                        'last_name' => $visit->salesRep->last_name,
                        'email' => $visit->salesRep->email,
                        'phone' => $visit->salesRep->contact_number
                    ] : null,
                    'sales_rep_name' => $visit->salesRep ? 
                        trim($visit->salesRep->first_name . ' ' . $visit->salesRep->last_name) : 
                        'Unknown',
                    
                    // GPS Coordinates
                    'checkin_latitude' => $visit->checkin_latitude ? (float) $visit->checkin_latitude : null,
                    'checkin_longitude' => $visit->checkin_longitude ? (float) $visit->checkin_longitude : null,
                    'checkout_latitude' => $visit->checkout_latitude ? (float) $visit->checkout_latitude : null,
                    'checkout_longitude' => $visit->checkout_longitude ? (float) $visit->checkout_longitude : null,
                    
                    // Visit proofs
                    'has_location_proof' => $visit->location_proof ?? false,
                    'has_photo_proof' => $visit->photo_proof ?? false,
                    'has_signature_proof' => $visit->signature_proof ?? false,
                    'has_video_proof' => $visit->video_proof ?? false,
                    'photo_urls' => $photoUrls, // ✅ Array of full photo URLs
                    'photos_count' => count($photoUrls), // ✅ Photo count
                    'photo_proof_paths' => $visit->photo_proof_paths, // Raw JSON (for reference)
                    
                    // Additional info
                    'remarks' => $visit->remarks,
                    'created_at' => $visit->created_at ? $visit->created_at->format('Y-m-d H:i:s') : null,
                    'created_by' => $visit->createdBy ? [
                        'id' => $visit->createdBy->id,
                        'name' => trim($visit->createdBy->first_name . ' ' . $visit->createdBy->last_name)
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'visits' => $formattedVisits,
                    'total' => $formattedVisits->count(),
                    'filters_applied' => [
                        'sales_rep_id' => $sales_rep_id != 'all' ? $sales_rep_id : 'all',
                        'status' => $request->status ?? 'all',
                        'date_from' => $request->date_from ?? 'none',
                        'date_to' => $request->date_to ?? 'none'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching recent visits: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales rep total activities statistics
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function salesRepActivities(Request $request)
    {
        try {
            $user = auth()->user();
            $business_id = $user->business_id;

            // Get sales rep ID from request or use logged-in user
            $sales_rep_id = $request->get('sales_rep_id', $user->id);

            // Validate sales rep exists and belongs to same business
            $salesRep = \App\User::where('id', $sales_rep_id)
                ->where('business_id', $business_id)
                ->first();

            if (!$salesRep) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales representative not found'
                ], 404);
            }

            // Date range filter (optional)
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Build base queries with date filters
            $leadsQuery = Lead::where('business_id', $business_id)
                ->where('sales_rep_id', $sales_rep_id);

            $visitsQuery = VisitTracking::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->where('sales_rep_id', $sales_rep_id);

            $ticketsQuery = \App\Ticket::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->where('user_id', $sales_rep_id);

            // Apply date filters if provided
            if ($startDate && $endDate) {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();

                $leadsQuery->whereBetween('created_at', [$start, $end]);
                $visitsQuery->whereBetween('start_time', [$start, $end]);
                $ticketsQuery->whereBetween('created_at', [$start, $end]);
            }

            // Get total counts
            $totalLeads = (clone $leadsQuery)->count();
            $totalVisits = (clone $visitsQuery)->count();
            $totalTickets = (clone $ticketsQuery)->count();

            // Get lead statistics by status
            $leadsByStatus = [
                'new' => (clone $leadsQuery)->where('lead_status', 'new')->count(),
                'in_progress' => (clone $leadsQuery)->where('lead_status', 'in_progress')->count(),
                'follow_up' => (clone $leadsQuery)->where('lead_status', 'follow_up')->count(),
                'converted' => (clone $leadsQuery)->where('lead_status', 'converted')->count(),
                'lost' => (clone $leadsQuery)->where('lead_status', 'lost')->count(),
                'qualified' => (clone $leadsQuery)->where('lead_status', 'qualified')->count(),
                'unqualified' => (clone $leadsQuery)->where('lead_status', 'unqualified')->count(),
            ];

            // Get visit statistics by status
            $visitsByStatus = [
                'completed' => (clone $visitsQuery)->where('status', 'completed')->count(),
                'pending' => (clone $visitsQuery)->where('status', 'pending')->count(),
                'in_progress' => (clone $visitsQuery)->where('status', 'in_progress')->count(),
                'cancelled' => (clone $visitsQuery)->where('status', 'cancelled')->count(),
            ];

            // Get ticket statistics by status
            $ticketsByStatus = [
                'open' => (clone $ticketsQuery)->where('status', 'open')->count(),
                'in_progress' => (clone $ticketsQuery)->where('status', 'in_progress')->count(),
                'pending' => (clone $ticketsQuery)->where('status', 'pending')->count(),
                'resolved' => (clone $ticketsQuery)->where('status', 'resolved')->count(),
                'closed' => (clone $ticketsQuery)->where('status', 'closed')->count(),
            ];

            // Calculate total activity score
            $totalActivities = $totalLeads + $totalVisits + $totalTickets;

            // Get recent activities (last 10)
            $recentLeads = (clone $leadsQuery)->orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'reference_no', 'store_name', 'lead_status', 'created_at'])
                ->map(function($lead) {
                    return [
                        'type' => 'lead',
                        'id' => $lead->id,
                        'reference' => $lead->reference_no,
                        'title' => $lead->store_name,
                        'status' => $lead->lead_status,
                        'date' => $lead->created_at->format('Y-m-d H:i:s')
                    ];
                });

            $recentVisits = (clone $visitsQuery)->orderBy('start_time', 'desc')
                ->take(5)
                ->with('lead:id,reference_no,store_name')
                ->get()
                ->map(function($visit) {
                    return [
                        'type' => 'visit',
                        'id' => $visit->id,
                        'reference' => $visit->lead ? $visit->lead->reference_no : 'N/A',
                        'title' => $visit->lead ? $visit->lead->store_name : 'Unknown',
                        'status' => $visit->status,
                        'date' => $visit->start_time ? $visit->start_time->format('Y-m-d H:i:s') : 'N/A'
                    ];
                });

            $recentActivities = $recentLeads->merge($recentVisits)
                ->sortByDesc('date')
                ->take(10)
                ->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'sales_rep' => [
                        'id' => $salesRep->id,
                        'name' => trim($salesRep->first_name . ' ' . $salesRep->last_name),
                        'email' => $salesRep->email,
                        'phone' => $salesRep->contact_number,
                    ],
                    'date_range' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    'summary' => [
                        'total_activities' => $totalActivities,
                        'total_leads' => $totalLeads,
                        'total_visits' => $totalVisits,
                        'total_tickets' => $totalTickets,
                    ],
                    'leads' => [
                        'total' => $totalLeads,
                        'by_status' => $leadsByStatus,
                    ],
                    'visits' => [
                        'total' => $totalVisits,
                        'by_status' => $visitsByStatus,
                    ],
                    'tickets' => [
                        'total' => $totalTickets,
                        'by_status' => $ticketsByStatus,
                    ],
                    'recent_activities' => $recentActivities,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sales rep activities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get lead statistics
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        try {
            $user = auth()->user();
            $business_id = $user->business_id;

            $query = Lead::where('business_id', $business_id);

            // Filter by sales rep if provided
            if ($request->has('sales_rep_id')) {
                $query->where('assigned_to', $request->sales_rep_id);
            }

            // Date range filter
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }

            $statistics = [
                'total_leads' => $query->count(),
                'by_status' => [
                    'new' => (clone $query)->where('status', 'new')->count(),
                    'contacted' => (clone $query)->where('status', 'contacted')->count(),
                    'qualified' => (clone $query)->where('status', 'qualified')->count(),
                    'proposal' => (clone $query)->where('status', 'proposal')->count(),
                    'negotiation' => (clone $query)->where('status', 'negotiation')->count(),
                    'won' => (clone $query)->where('status', 'won')->count(),
                    'lost' => (clone $query)->where('status', 'lost')->count(),
                ],
                'by_priority' => [
                    'high' => (clone $query)->where('priority', 'high')->count(),
                    'medium' => (clone $query)->where('priority', 'medium')->count(),
                    'low' => (clone $query)->where('priority', 'low')->count(),
                ],
                'conversion_rate' => [
                    'won' => (clone $query)->where('status', 'won')->count(),
                    'total' => $query->count(),
                    'percentage' => $query->count() > 0 ? 
                        round(((clone $query)->where('status', 'won')->count() / $query->count()) * 100, 2) : 0
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}