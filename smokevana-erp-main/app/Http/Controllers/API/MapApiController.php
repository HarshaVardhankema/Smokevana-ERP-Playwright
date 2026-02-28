<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Lead;
use App\Contact;
use App\VisitTracking;
use App\BusinessLocation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MapApiController extends Controller
{
    /**
     * Debug endpoint - Get database stats for leads
     */
    public function getLeadsStats(Request $request)
    {
        try {
            $business_id = $request->user()->business_id;
            
            $stats = [
                'total_leads' => Lead::where('business_id', $business_id)->count(),
                'leads_with_coords' => Lead::where('business_id', $business_id)
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->count(),
                'leads_with_zero_coords' => Lead::where('business_id', $business_id)
                    ->where(function($q) {
                        $q->where('latitude', 0)
                          ->orWhere('longitude', 0);
                    })
                    ->count(),
                'leads_with_valid_coords' => Lead::where('business_id', $business_id)
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    ->count(),
                'sample_leads' => Lead::where('business_id', $business_id)
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->select('id', 'store_name', 'latitude', 'longitude', 'city', 'state', 'sales_rep_id')
                    ->limit(10)
                    ->get()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DEPRECATED: Get all leads for map display
     * Redirects to unified /staff/leads API with map parameters
     * @deprecated Use /staff/leads?with_coordinates_only=true&no_pagination=true instead
     */
    public function getLeads(Request $request)
    {
        // Redirect to unified leads API with map parameters
        $request->merge([
            'with_coordinates_only' => true,
            'no_pagination' => true
        ]);
        
        return app(\App\Http\Controllers\API\LeadApiController::class)->index($request);
    }

    /**
     * Get visit tracking data for map display with complete sales rep info
     */
    public function getVisits(Request $request)
    {
        try {
            $business_id = $request->user()->business_id;
            $sales_rep_id = $request->input('sales_rep_id');
            $date_from = $request->input('date_from');
            $date_to = $request->input('date_to');
            
            $query = VisitTracking::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->whereNotNull('checkin_latitude')
                ->whereNotNull('checkin_longitude')
                ->with([
                    'lead' => function($q) {
                        $q->select('id', 'store_name', 'company_name', 'city');
                    },
                    'salesRep' => function($q) {
                        $q->select('id', 'first_name', 'last_name', 'email', 'contact_number');
                    }
                ]);
            
            if ($sales_rep_id) {
                $query->where('sales_rep_id', $sales_rep_id);
            }
            
            if ($date_from) {
                $query->whereDate('start_time', '>=', $date_from);
            }
            
            if ($date_to) {
                $query->whereDate('start_time', '<=', $date_to);
            }
            
            $visits = $query->orderBy('start_time', 'desc')
                ->limit(200)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $visits->map(function($visit) {
                    $salesRep = null;
                    if ($visit->salesRep) {
                        $salesRep = [
                            'id' => $visit->salesRep->id,
                            'name' => trim($visit->salesRep->first_name . ' ' . $visit->salesRep->last_name),
                            'first_name' => $visit->salesRep->first_name,
                            'last_name' => $visit->salesRep->last_name,
                            'email' => $visit->salesRep->email,
                            'phone' => $visit->salesRep->contact_number
                        ];
                    }
                    
                    return [
                        'id' => $visit->id,
                        'lead_id' => $visit->lead_id,
                        'store_name' => $visit->lead->store_name ?? 'Unknown',
                        'business_name' => $visit->lead->company_name ?? $visit->lead->store_name ?? '',
                        'company_name' => $visit->lead->company_name ?? '',
                        'city' => $visit->lead->city ?? '',
                        'sales_rep_id' => $visit->sales_rep_id,
                        'sales_rep' => $salesRep,
                        'sales_rep_name' => $salesRep ? $salesRep['name'] : 'N/A',
                        'latitude' => (float) $visit->checkin_latitude,
                        'longitude' => (float) $visit->checkin_longitude,
                        'checkout_latitude' => $visit->checkout_latitude ? (float) $visit->checkout_latitude : null,
                        'checkout_longitude' => $visit->checkout_longitude ? (float) $visit->checkout_longitude : null,
                        'status' => $visit->status,
                        'start_time' => $visit->start_time->format('Y-m-d H:i:s'),
                        'start_time_formatted' => $visit->start_time->format('M d, Y h:i A'),
                        'checkout_time' => $visit->checkout_time ? $visit->checkout_time->format('Y-m-d H:i:s') : null,
                        'duration' => $visit->duration,
                        'visit_type' => $visit->visit_type,
                        'remarks' => $visit->remarks
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching visits: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nearby leads within a radius (for map view)
     */
    public function getNearbyLeads(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'nullable|numeric|min:1|max:50',
                'sales_rep_id' => 'nullable|integer|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $business_id = $request->user()->business_id;
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius', 5); // Default 5km radius
            $sales_rep_id = $request->input('sales_rep_id');
            
            // Get leads within radius using Haversine formula
            $haversine = sprintf(
                '(6371 * acos(cos(radians(%s)) * cos(radians(latitude)) * cos(radians(longitude) - radians(%s)) + sin(radians(%s)) * sin(radians(latitude))))',
                $latitude,
                $longitude,
                $latitude
            );

            $currentUserId = $request->user()->id;

            $query = Lead::where('business_id', $business_id)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', 0)
                ->where('longitude', '!=', 0);
            
            // Filter by sales rep if provided
            if ($sales_rep_id) {
                $query->where('sales_rep_id', $sales_rep_id);
            }
            
            // Always include visited leads for follow-ups - no filtering based on visit status
            
            $nearbyLeads = $query->selectRaw("*, {$haversine} AS distance")
                ->whereRaw("{$haversine} < ?", [$radius])
                ->with(['salesRep:id,first_name,last_name,email'])
                ->orderBy('distance')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'leads' => $nearbyLeads->map(function($lead) use ($currentUserId) {
                        // Check if current user can visit this lead
                        $canVisit = true;
                        $cannotVisitReason = null;
                        $assignedToOtherRep = false;
                        
                        // Get visit statistics
                        $totalVisits = VisitTracking::where('lead_id', $lead->id)
                            ->where('status', 'completed')
                            ->count();
                        
                        $currentUserVisits = VisitTracking::where('lead_id', $lead->id)
                            ->where('sales_rep_id', $currentUserId)
                            ->where('status', 'completed')
                            ->count();
                        
                        // Get last completed visit (by anyone)
                        $lastVisit = VisitTracking::where('lead_id', $lead->id)
                            ->where('status', 'completed')
                            ->orderBy('checkout_time', 'desc')
                            ->with('salesRep:id,first_name,last_name')
                            ->first();
                        
                        // Check if lead has been VISITED (completed visit) by someone else
                        // Only block if there's a completed visit by another sales rep
                        $firstCompletedVisit = VisitTracking::where('lead_id', $lead->id)
                            ->where('status', 'completed')
                            ->orderBy('checkout_time', 'asc')
                            ->first();
                        
                        if ($firstCompletedVisit && $firstCompletedVisit->sales_rep_id != $currentUserId) {
                            $canVisit = false;
                            $assignedToOtherRep = true;
                            
                            // Get the assigned sales rep's name
                            $assignedSalesRep = \App\User::find($firstCompletedVisit->sales_rep_id);
                            $assignedRepName = $assignedSalesRep 
                                ? trim($assignedSalesRep->first_name . ' ' . $assignedSalesRep->last_name)
                                : 'Unknown';
                            
                            $cannotVisitReason = "Already visited by {$assignedRepName}";
                        }
                        
                        // Check if there's an active visit (in_progress)
                        $activeVisit = VisitTracking::where('lead_id', $lead->id)
                            ->where('status', 'in_progress')
                            ->first();
                        
                        $hasActiveVisit = $activeVisit ? true : false;
                        $activeVisitBySelf = $activeVisit && $activeVisit->sales_rep_id == $currentUserId;
                        
                        // Get who visited this lead (from first completed visit)
                        $visitedBy = null;
                        if ($firstCompletedVisit) {
                            $visitedBySalesRep = \App\User::find($firstCompletedVisit->sales_rep_id);
                            if ($visitedBySalesRep) {
                                $visitedBy = [
                                    'id' => $visitedBySalesRep->id,
                                    'name' => trim($visitedBySalesRep->first_name . ' ' . $visitedBySalesRep->last_name),
                                    'first_name' => $visitedBySalesRep->first_name,
                                    'last_name' => $visitedBySalesRep->last_name
                                ];
                            }
                        }
                        
                        return [
                            'id' => $lead->id,
                            'reference_no' => $lead->reference_no,
                            'store_name' => $lead->store_name,
                            'company_name' => $lead->company_name,
                            'contact_name' => $lead->contact_name,
                            'contact_phone' => $lead->contact_phone,
                            'contact_email' => $lead->contact_email,
                            'address' => $lead->full_address,
                            'city' => $lead->city,
                            'state' => $lead->state,
                            'latitude' => (float) $lead->latitude,
                            'longitude' => (float) $lead->longitude,
                            'distance' => round($lead->distance, 2) . ' km',
                            'distance_km' => round($lead->distance, 2),
                            'lead_status' => $lead->lead_status,
                            'sales_rep_id' => $lead->sales_rep_id,
                            'sales_rep' => $lead->salesRep ? [
                                'id' => $lead->salesRep->id,
                                'name' => trim($lead->salesRep->first_name . ' ' . $lead->salesRep->last_name),
                                'first_name' => $lead->salesRep->first_name,
                                'last_name' => $lead->salesRep->last_name,
                                'email' => $lead->salesRep->email
                            ] : null,
                            
                            // Visit permission fields
                            'can_visit' => $canVisit,
                            'assigned_to_other_rep' => $assignedToOtherRep,
                            'cannot_visit_reason' => $cannotVisitReason,
                            
                            // Visit history information
                            'has_been_visited' => $totalVisits > 0,
                            'total_visits' => $totalVisits,
                            'my_visits' => $currentUserVisits,
                            'visited_by_me' => $currentUserVisits > 0,
                            'visited_by' => $visitedBy,
                            'last_visit' => $lastVisit ? [
                                'date' => $lastVisit->checkout_time ? $lastVisit->checkout_time->format('Y-m-d H:i:s') : null,
                                'date_formatted' => $lastVisit->checkout_time ? $lastVisit->checkout_time->format('M d, Y h:i A') : null,
                                'by' => $lastVisit->salesRep ? trim($lastVisit->salesRep->first_name . ' ' . $lastVisit->salesRep->last_name) : 'Unknown',
                                'by_me' => $lastVisit->sales_rep_id == $currentUserId
                            ] : null,
                            'has_active_visit' => $hasActiveVisit,
                            'active_visit_by_me' => $activeVisitBySelf
                        ];
                    }),
                    'total' => $nearbyLeads->count(),
                    'radius_km' => $radius,
                    'search_location' => [
                        'latitude' => (float) $latitude,
                        'longitude' => (float) $longitude
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching nearby leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DEPRECATED: Get nearby stores - redirects to getNearbyLeads
     * @deprecated Use getNearbyLeads() instead
     */
    public function getNearbyStores(Request $request)
    {
        // Redirect to new method for backward compatibility
        return $this->getNearbyLeads($request);
    }

    /**
     * Add a nearby lead discovered during field visit
     * Creates both a Lead record and a NearbyStore record for tracking
     */
    public function addNearbyLead(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'contact_person' => 'nullable|string|max:255',
                'contact_number' => 'nullable|string|max:50',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $business_id = $request->user()->business_id;
            $user_id = $request->user()->id;

            // Generate unique reference number for the lead
            $lastLead = Lead::where('business_id', $business_id)
                ->orderBy('id', 'desc')
                ->first();
            
            $nextNumber = $lastLead ? ((int) substr($lastLead->reference_no, 2)) + 1 : 1;
            $reference_no = 'LD' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Create the Lead directly
            $lead = Lead::create([
                'business_id' => $business_id,
                'reference_no' => $reference_no,
                'store_name' => $request->store_name,
                'contact_name' => $request->contact_person,
                'contact_phone' => $request->contact_number,
                'full_address' => $request->address,
                'address_line_1' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'lead_status' => 'new',
                'source' => 'Field Discovery',
                'created_by' => $user_id,
                'assigned_to' => $user_id,
                'sales_rep_id' => $user_id,
                'discovered_during_field_visit' => true
            ]);

            // Also create a NearbyStore record for tracking purposes (if model exists)
            $nearbyStore = null;
            if (class_exists(\App\NearbyStore::class)) {
                try {
                    $nearbyStore = \App\NearbyStore::create([
                        'business_id' => $business_id,
                        'store_name' => $request->store_name,
                        'address' => $request->address,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'contact_person' => $request->contact_person,
                        'contact_number' => $request->contact_number,
                        'notes' => $request->notes,
                        'added_by' => $user_id,
                        'discovered_by_sales_rep_id' => $user_id,
                        'discovery_date' => now(),
                        'is_converted_to_lead' => true,
                        'converted_to_lead_id' => $lead->id
                    ]);
                } catch (\Throwable $e) {
                    \Log::warning('NearbyStore create failed: ' . $e->getMessage());
                }
            } else {
                \Log::notice('NearbyStore model not present; skipping NearbyStore creation');
            }

            return response()->json([
                'success' => true,
                'message' => 'Nearby lead added successfully and will appear in leads list',
                'data' => [
                    'lead' => [
                        'id' => $lead->id,
                        'reference_no' => $lead->reference_no,
                        'store_name' => $lead->store_name,
                        'contact_name' => $lead->contact_name,
                        'contact_phone' => $lead->contact_phone,
                        'full_address' => $lead->full_address,
                        'latitude' => $lead->latitude ? (float) $lead->latitude : null,
                        'longitude' => $lead->longitude ? (float) $lead->longitude : null,
                        'lead_status' => $lead->lead_status,
                        'source' => $lead->source,
                        'created_at' => $lead->created_at->toDateTimeString()
                    ],
                    'nearby_store_id' => $nearbyStore->id ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding nearby lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DEPRECATED: Add a nearby store - redirects to addNearbyLead
     * @deprecated Use addNearbyLead() instead
     */
    public function addNearbyStore(Request $request)
    {
        // Redirect to new method for backward compatibility
        return $this->addNearbyLead($request);
    }

    /**
     * Create a visit with GPS coordinates (Check-in)
     */
    public function createVisit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'notes' => 'nullable|string',
                'visit_type' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $business_id = $request->user()->business_id;
            $user_id = $request->user()->id;
            
            // ============================================
            // SALES REP ASSIGNMENT VALIDATION
            // ============================================
            // Check if this lead has been visited before by another sales rep
            $firstCompletedVisit = VisitTracking::where('lead_id', $request->lead_id)
                ->where('status', 'completed')
                ->orderBy('checkout_time', 'asc')
                ->first();
            
            if ($firstCompletedVisit && $firstCompletedVisit->sales_rep_id != $user_id) {
                // Get the assigned sales rep's name
                $assignedSalesRep = \App\User::find($firstCompletedVisit->sales_rep_id);
                $assignedSalesRepName = $assignedSalesRep 
                    ? trim($assignedSalesRep->first_name . ' ' . $assignedSalesRep->last_name)
                    : 'Unknown';
                
                return response()->json([
                    'success' => false,
                    'message' => 'This lead is assigned to ' . $assignedSalesRepName,
                    'error_code' => 'LEAD_ASSIGNED_TO_DIFFERENT_SALES_REP',
                    'assigned_to' => [
                        'id' => $firstCompletedVisit->sales_rep_id,
                        'name' => $assignedSalesRepName
                    ]
                ], 403);
            }
            
            // Create visit tracking
            $visit = VisitTracking::create([
                'business_id' => $business_id,
                'sales_rep_id' => $user_id,
                'lead_id' => $request->lead_id,
                'start_time' => now(),
                'status' => 'in_progress',
                'visit_type' => $request->visit_type ?? 'initial',
                'checkin_latitude' => $request->latitude,
                'checkin_longitude' => $request->longitude,
                'remarks' => $request->notes,
                'location_proof' => true,
                'created_by' => $user_id
            ]);
            
            // Update lead's last contact date and assign sales rep if first visit
            $leadUpdateData = [
                'last_contact_date' => now()
            ];
            
            // If this is the first visit, assign the sales rep to the lead
            if (!$firstCompletedVisit) {
                $leadUpdateData['sales_rep_id'] = $user_id;
                $leadUpdateData['visited_by'] = $user_id;
            }
            
            Lead::where('id', $request->lead_id)->update($leadUpdateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Visit created successfully',
                'data' => $visit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating visit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete a visit (checkout) with photo upload
     */
    public function completeVisit(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'duration' => 'nullable|numeric',
                'notes' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max - single photo
                'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120' // Multiple photos support
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $visit = VisitTracking::findOrFail($id);
            
            // Calculate duration if not provided
            $duration = $request->duration;
            if (!$duration && $visit->start_time) {
                $duration = now()->diffInMinutes($visit->start_time);
            }
            
            // Handle photo uploads
            $photoPaths = [];
            $hasPhoto = false;
            
            // Create directory if it doesn't exist
            if (!file_exists(public_path('uploads/visits'))) {
                mkdir(public_path('uploads/visits'), 0755, true);
            }
            
            // Single photo upload
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'visit_' . $visit->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/visits'), $filename);
                $photoPaths[] = $filename;
                $hasPhoto = true;
            }
            
            // Multiple photos upload
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $file) {
                    $filename = 'visit_' . $visit->id . '_' . time() . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/visits'), $filename);
                    $photoPaths[] = $filename;
                    $hasPhoto = true;
                }
            }
            
            $visit->update([
                'checkout_latitude' => $request->latitude,
                'checkout_longitude' => $request->longitude,
                'checkout_time' => now(),
                'duration' => $duration,
                'status' => 'completed',
                'remarks' => $request->notes ?? $visit->remarks,
                'photo_proof' => $hasPhoto,
                'photo_proof_paths' => $hasPhoto ? json_encode($photoPaths) : null
            ]);
            
            // Update lead's last_contact_date and visited_by to mark it as visited
            if ($visit->lead_id) {
                Lead::where('id', $visit->lead_id)->update([
                    'last_contact_date' => now(),
                    'visited_by' => $visit->sales_rep_id,
                    'status' => 'visited' // Update status to visited
                ]);
            }
            
            // Prepare response with photo URLs
            $visitData = $visit->toArray();
            if ($hasPhoto && !empty($photoPaths)) {
                $visitData['photo_urls'] = array_map(function($path) {
                    return url('uploads/visits/' . $path);
                }, $photoPaths);
                $visitData['photos_count'] = count($photoPaths);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Visit completed successfully' . ($hasPhoto ? ' with ' . count($photoPaths) . ' photo(s)' : ''),
                'data' => $visitData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing visit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current live locations of sales reps
     */
    public function getSalesRepLocations(Request $request)
    {
        try {
            $business_id = $request->user()->business_id;
            
            // Get active visits (in_progress status) with latest GPS coordinates
            $activeVisits = VisitTracking::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->where('status', 'in_progress')
                ->whereNotNull('checkin_latitude')
                ->whereNotNull('checkin_longitude')
                ->with(['salesRep', 'lead'])
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $activeVisits->map(function($visit) {
                    return [
                        'sales_rep_id' => $visit->sales_rep_id,
                        'sales_rep_name' => $visit->salesRep ? 
                            $visit->salesRep->first_name . ' ' . $visit->salesRep->last_name : 'N/A',
                        'latitude' => (float) $visit->checkin_latitude,
                        'longitude' => (float) $visit->checkin_longitude,
                        'current_lead' => $visit->lead->store_name ?? 'Unknown',
                        'visit_started' => $visit->start_time->format('M d, Y h:i A'),
                        'duration_so_far' => now()->diffInMinutes($visit->start_time) . ' min'
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sales rep locations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update current location for a sales rep
     */
    public function updateCurrentLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'accuracy' => 'nullable|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user_id = $request->user()->id;
            
            // Store location in session or cache for real-time tracking
            \Cache::put("sales_rep_location_{$user_id}", [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'timestamp' => now()->toIso8601String()
            ], now()->addMinutes(10));
            
            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating location: ' . $e->getMessage()
            ], 500);
        }
    }
}

