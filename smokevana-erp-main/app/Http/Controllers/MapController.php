<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\ModuleUtil;
use App\Lead;
use App\Contact;
use App\VisitTracking;
use App\BusinessLocation;
use App\User;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    /**
     * Display the maps page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');
        
        // Get filter parameters
        $sales_rep_id = request()->input('sales_rep_id');
        $status = request()->input('status');
        $date_from = request()->input('date_from');
        $date_to = request()->input('date_to');
        
        // Get all business locations
        $locations = BusinessLocation::where('business_id', $business_id)->get();
        
        // Get all leads with GPS coordinates (with filters)
        $leadsQuery = Lead::where('business_id', $business_id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['salesRep' => function($q) {
                $q->select('id', 'first_name', 'last_name');
            }])
            ->select('id', 'store_name', 'contact_name', 'contact_phone', 'lead_status', 
                    'latitude', 'longitude', 'city', 'state', 'sales_rep_id', 'reference_no');
        
        // Apply filters
        if ($sales_rep_id) {
            $leadsQuery->where('sales_rep_id', $sales_rep_id);
        }
        
        if ($status) {
            $leadsQuery->where('lead_status', $status);
        }
        
        $leads = $leadsQuery->get();
        
        // Get all customers with GPS coordinates (contacts table doesn't have GPS columns yet)
        $customers = collect(); // Empty collection for now
        
        // Get recent visit tracking with GPS coordinates (with filters)
        $visitsQuery = VisitTracking::whereHas('lead', function($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })
            ->whereNotNull('checkin_latitude')
            ->whereNotNull('checkin_longitude')
            ->with([
                'lead' => function($q) {
                    $q->select('id', 'store_name');
                },
                'salesRep' => function($q) {
                    $q->select('id', 'first_name', 'last_name');
                }
            ])
            ->select('id', 'lead_id', 'sales_rep_id', 'checkin_latitude', 'checkin_longitude', 
                    'start_time', 'duration', 'status');
        
        // Apply visit filters
        if ($sales_rep_id) {
            $visitsQuery->where('sales_rep_id', $sales_rep_id);
        }
        
        if ($date_from) {
            $visitsQuery->whereDate('start_time', '>=', $date_from);
        }
        
        if ($date_to) {
            $visitsQuery->whereDate('start_time', '<=', $date_to);
        }
        
        $visits = $visitsQuery->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
        
        // Get nearby stores if model exists (with error handling)
        if (class_exists(\App\NearbyStore::class)) {
            try {
                $nearbyStores = \App\NearbyStore::where('business_id', $business_id)
                    ->when(method_exists(\App\NearbyStore::class, 'scopeNotConverted'), function ($q) {
                        // Call scope only if it exists
                        return $q->notConverted();
                    })
                    ->with(['discoveredBySalesRep' => function($q) {
                        $q->select('id', 'first_name', 'last_name');
                    }])
                    ->select('id', 'store_name', 'address', 'latitude', 'longitude', 
                            'contact_person', 'contact_number', 'discovered_by_sales_rep_id')
                    ->get();
            } catch (\Throwable $e) {
                // If table doesn't exist or other error, use empty collection
                $nearbyStores = collect();
                \Log::warning('NearbyStore query failed: ' . $e->getMessage());
            }
        } else {
            $nearbyStores = collect();
        }
        
        // Get all sales reps for filter (users with sales rep role)
        $salesReps = User::where('business_id', $business_id)
            ->where(function($query) use ($user_id) {
                $query->whereHas('roles', function($q) {
                    $q->where('name', 'LIKE', '%Sales%')
                      ->orWhere('name', 'LIKE', '%sales%')
                      ->orWhere('name', 'LIKE', '%Rep%');
                })
                ->orWhere('id', $user_id);
            })
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();
        
        // Get current user location from cache if available
        $currentLocation = \Cache::get("sales_rep_location_{$user_id}");
        
        return view('maps.admin_map')
            ->with(compact('locations', 'leads', 'customers', 'visits', 'nearbyStores', 'salesReps', 'currentLocation'));
    }

    /**
     * Sales rep focused map view
     */
    public function salesRepMap()
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');
        
        // Get leads assigned to this sales rep
        $myLeads = Lead::where('business_id', $business_id)
            ->where('sales_rep_id', $user_id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'store_name', 'business_name', 'contact_name', 'contact_phone', 
                    'lead_status', 'latitude', 'longitude', 'city', 'state', 'address_line_1')
            ->get();
        
        // Get my recent visits
        $myVisits = VisitTracking::where('sales_rep_id', $user_id)
            ->whereNotNull('checkin_latitude')
            ->whereNotNull('checkin_longitude')
            ->with(['lead' => function($q) {
                $q->select('id', 'store_name');
            }])
            ->select('id', 'lead_id', 'checkin_latitude', 'checkin_longitude', 
                    'start_time', 'duration', 'status')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
        
        // Get current location from cache
        $currentLocation = \Cache::get("sales_rep_location_{$user_id}");
        
        return view('maps.sales_rep_map_new')
            ->with(compact('myLeads', 'myVisits', 'currentLocation'));
    }

    /**
     * Get leads data for web
     */
    public function getLeadsData()
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $sales_rep_id = request()->input('sales_rep_id');
            
            $leads = Lead::where('business_id', $business_id)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', 0)
                ->where('longitude', '!=', 0)
                ->when($sales_rep_id, function($query) use ($sales_rep_id) {
                    return $query->where('sales_rep_id', $sales_rep_id);
                })
                ->with(['salesRep' => function($q) {
                    $q->select('id', 'first_name', 'last_name', 'email', 'contact_number');
                }])
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $leads->map(function($lead) {
                    return [
                        'id' => $lead->id,
                        'store_name' => $lead->store_name,
                        'company_name' => $lead->company_name,
                        'contact_name' => $lead->contact_name,
                        'contact_phone' => $lead->contact_phone,
                        'status' => $lead->lead_status,
                        'latitude' => (float) $lead->latitude,
                        'longitude' => (float) $lead->longitude,
                        'city' => $lead->city,
                        'state' => $lead->state,
                        'sales_rep' => $lead->salesRep ? [
                            'id' => $lead->salesRep->id,
                            'name' => trim($lead->salesRep->first_name . ' ' . $lead->salesRep->last_name),
                            'email' => $lead->salesRep->email,
                            'phone' => $lead->salesRep->contact_number
                        ] : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get visits data for web
     */
    public function getVisitsData()
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $sales_rep_id = request()->input('sales_rep_id');
            $date_from = request()->input('date_from');
            $date_to = request()->input('date_to');
            
            $query = VisitTracking::whereHas('lead', function($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                })
                ->whereNotNull('checkin_latitude')
                ->whereNotNull('checkin_longitude')
                ->with([
                    'lead' => function($q) {
                        $q->select('id', 'store_name', 'company_name');
                    },
                    'salesRep' => function($q) {
                        $q->select('id', 'first_name', 'last_name', 'email');
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
                    return [
                        'id' => $visit->id,
                        'lead_id' => $visit->lead_id,
                        'store_name' => $visit->lead->store_name ?? 'Unknown',
                        'company_name' => $visit->lead->company_name ?? '',
                        'sales_rep' => $visit->salesRep ? [
                            'id' => $visit->salesRep->id,
                            'name' => trim($visit->salesRep->first_name . ' ' . $visit->salesRep->last_name),
                            'email' => $visit->salesRep->email
                        ] : null,
                        'latitude' => (float) $visit->checkin_latitude,
                        'longitude' => (float) $visit->checkin_longitude,
                        'status' => $visit->status,
                        'start_time' => $visit->start_time->format('Y-m-d H:i:s'),
                        'duration' => $visit->duration,
                        'visit_type' => $visit->visit_type
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
     * Get nearby leads for web
     */
    public function getNearbyLeadsData()
    {
        try {
            $validator = \Validator::make(request()->all(), [
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

            $business_id = request()->session()->get('user.business_id');
            $latitude = request()->input('latitude');
            $longitude = request()->input('longitude');
            $radius = request()->input('radius', 5); // Default 5km radius
            $sales_rep_id = request()->input('sales_rep_id');
            
            // Haversine formula to calculate distance
            $haversine = "(6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude))))";

            $query = Lead::where('business_id', $business_id)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', 0)
                ->where('longitude', '!=', 0);
            
            if ($sales_rep_id) {
                $query->where('sales_rep_id', $sales_rep_id);
            }
            
            $nearbyLeads = $query->selectRaw("*, {$haversine} AS distance")
                ->whereRaw("{$haversine} < ?", [$radius])
                ->with(['salesRep' => function($q) {
                    $q->select('id', 'first_name', 'last_name', 'email');
                }])
                ->orderBy('distance')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'leads' => $nearbyLeads->map(function($lead) {
                        return [
                            'id' => $lead->id,
                            'store_name' => $lead->store_name,
                            'company_name' => $lead->company_name,
                            'contact_name' => $lead->contact_name,
                            'contact_phone' => $lead->contact_phone,
                            'status' => $lead->lead_status,
                            'latitude' => (float) $lead->latitude,
                            'longitude' => (float) $lead->longitude,
                            'distance' => round($lead->distance, 2) . ' km',
                            'distance_km' => round($lead->distance, 2),
                            'sales_rep' => $lead->salesRep ? [
                                'id' => $lead->salesRep->id,
                                'name' => trim($lead->salesRep->first_name . ' ' . $lead->salesRep->last_name),
                                'email' => $lead->salesRep->email
                            ] : null
                        ];
                    })
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
     * Get sales rep locations for web
     */
    public function getSalesRepLocationsData()
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            
            $locations = \DB::table('sales_rep_locations')
                ->join('users', 'sales_rep_locations.user_id', '=', 'users.id')
                ->where('users.business_id', $business_id)
                ->select(
                    'users.id as user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'sales_rep_locations.latitude',
                    'sales_rep_locations.longitude',
                    'sales_rep_locations.accuracy',
                    'sales_rep_locations.heading',
                    'sales_rep_locations.speed',
                    'sales_rep_locations.updated_at as last_updated'
                )
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $locations->map(function($location) {
                    return [
                        'user_id' => $location->user_id,
                        'name' => trim($location->first_name . ' ' . $location->last_name),
                        'email' => $location->email,
                        'latitude' => (float) $location->latitude,
                        'longitude' => (float) $location->longitude,
                        'accuracy' => $location->accuracy,
                        'heading' => $location->heading,
                        'speed' => $location->speed,
                        'last_updated' => $location->last_updated,
                        'last_updated_formatted' => \Carbon\Carbon::parse($location->last_updated)->diffForHumans()
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
     * Create a new visit with GPS coordinates (for web)
     */
    public function createVisit(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'notes' => 'nullable|string',
                'visit_type' => 'nullable|string|in:initial,follow_up,demo,meeting,other'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');
            
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
            
            // Update lead's last contact date
            Lead::where('id', $request->lead_id)->update([
                'last_contact_date' => now()
            ]);
            
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
     * Complete a visit (checkout) - for web
     */
    public function completeVisit(Request $request, $id)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'duration' => 'nullable|numeric',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $visit = VisitTracking::findOrFail($id);
            
            // Verify ownership
            if ($visit->sales_rep_id != $request->session()->get('user.id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this visit'
                ], 403);
            }
            
            // Calculate duration if not provided
            $duration = $request->duration;
            if (!$duration && $visit->start_time) {
                $duration = now()->diffInMinutes($visit->start_time);
            }
            
            $visit->update([
                'checkout_latitude' => $request->latitude,
                'checkout_longitude' => $request->longitude,
                'checkout_time' => now(),
                'duration' => $duration,
                'status' => 'completed',
                'remarks' => $request->notes ?? $visit->remarks
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Visit completed successfully',
                'data' => $visit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing visit: ' . $e->getMessage()
            ], 500);
        }
    }
}


