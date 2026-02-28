<?php

namespace App\Http\Controllers;

use App\Lead;
use App\User;
use App\VisitTracking;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VisitHistoryController extends Controller
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
     * Display visit history page with map and table
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = VisitTracking::whereHas('lead', function ($q) use ($business_id) {
                $q->where('business_id', $business_id);
            })
                ->with(['lead:id,reference_no,store_name,company_name,contact_name,contact_phone,city,state,full_address,latitude,longitude', 'salesRep:id,first_name,last_name,email'])
                ->select('visit_tracking.*');

            // Apply filters
            if ($request->has('sales_rep_id') && $request->sales_rep_id != '') {
                $query->where('sales_rep_id', $request->sales_rep_id);
            }

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('start_time', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('start_time', '<=', $request->date_to);
            }

            return Datatables::of($query)
                ->addColumn('visit_reference', function ($row) {
                    return $row->visit_reference ?? 'N/A';
                })
                ->addColumn('lead_info', function ($row) {
                    if (!$row->lead) {
                        return 'N/A';
                    }
                    $html = '<strong>' . $row->lead->store_name . '</strong><br>';
                    $html .= '<small class="text-muted">' . ($row->lead->reference_no ?? '') . '</small>';
                    return $html;
                })
                ->addColumn('sales_rep_name', function ($row) {
                    if (!$row->salesRep) {
                        return 'N/A';
                    }
                    return trim($row->salesRep->first_name . ' ' . $row->salesRep->last_name);
                })
                ->addColumn('location', function ($row) {
                    if (!$row->lead) {
                        return 'N/A';
                    }
                    $city = $row->lead->city ?? '';
                    $state = $row->lead->state ?? '';
                    return $city . ($city && $state ? ', ' : '') . $state;
                })
                ->addColumn('visit_time', function ($row) {
                    $html = '<strong>Start:</strong> ' . ($row->start_time ? $row->start_time->format('M d, Y h:i A') : 'N/A') . '<br>';
                    if ($row->checkout_time) {
                        $html .= '<strong>End:</strong> ' . $row->checkout_time->format('M d, Y h:i A');
                    }
                    return $html;
                })
                ->addColumn('duration', function ($row) {
                    return $row->duration ?? 'N/A';
                })
                ->addColumn('visit_type', function ($row) {
                    if (!$row->visit_type) {
                        return '<span class="label label-default">N/A</span>';
                    }
                    $typeColors = [
                        'sales_visit' => 'primary',
                        'follow_up' => 'info',
                        'delivery' => 'success',
                        'collection' => 'warning',
                        'service' => 'danger',
                        'other' => 'default'
                    ];
                    $color = $typeColors[$row->visit_type] ?? 'default';
                    return '<span class="label label-' . $color . '">' . ucfirst(str_replace('_', ' ', $row->visit_type)) . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $statusColors = [
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger'
                    ];
                    $color = $statusColors[$row->status] ?? 'default';
                    return '<span class="label label-' . $color . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div>';
                    
                    // View details button
                    $html .= '<button type="button" class="btn btn-xs btn-primary view-details" 
                                data-id="' . $row->id . '" 
                                title="View Details" 
                                style="padding: 5px 12px;">
                                <i class="fa fa-eye" aria-hidden="true"></i> <span style="color: white;">View</span>
                              </button>';
                    
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['lead_info', 'visit_time', 'visit_type', 'status_badge', 'action'])
                ->make(true);
        }

        // Get sales reps for filter dropdown
        $salesReps = User::forDropdown($business_id, false, true, false);

        return view('visit_history.index')
            ->with(compact('salesReps'));
    }

    /**
     * Get visit details
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $visit = VisitTracking::whereHas('lead', function ($query) use ($business_id) {
            $query->where('business_id', $business_id);
        })
            ->with(['lead', 'salesRep'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $visit->id,
                'visit_reference' => $visit->visit_reference,
                'lead' => [
                    'id' => $visit->lead->id,
                    'reference_no' => $visit->lead->reference_no,
                    'store_name' => $visit->lead->store_name,
                    'contact_name' => $visit->lead->contact_name,
                    'contact_phone' => $visit->lead->contact_phone,
                    'address' => $visit->lead->full_address
                ],
                'sales_rep' => [
                    'id' => $visit->salesRep->id,
                    'name' => trim($visit->salesRep->first_name . ' ' . $visit->salesRep->last_name),
                    'email' => $visit->salesRep->email
                ],
                'start_time' => $visit->start_time ? $visit->start_time->format('M d, Y h:i A') : null,
                'checkout_time' => $visit->checkout_time ? $visit->checkout_time->format('M d, Y h:i A') : null,
                'duration' => $visit->duration,
                'visit_type' => $visit->visit_type,
                'status' => $visit->status,
                'remarks' => $visit->remarks,
                'checkin_latitude' => $visit->checkin_latitude,
                'checkin_longitude' => $visit->checkin_longitude,
                'checkout_latitude' => $visit->checkout_latitude,
                'checkout_longitude' => $visit->checkout_longitude,
                'proofs' => [
                    'has_location_proof' => $visit->location_proof,
                    'location_proof_path' => $visit->location_proof_path,
                    'has_photo_proof' => $visit->photo_proof,
                    'photo_proof_paths' => $visit->photo_proof_paths ?? [],
                    'has_signature_proof' => $visit->signature_proof,
                    'signature_proof_path' => $visit->signature_proof_path,
                    'has_video_proof' => $visit->video_proof,
                    'video_proof_path' => $visit->video_proof_path
                ]
            ]
        ]);
    }

    /**
     * Get visits for map view
     *
     * @return \Illuminate\Http\Response
     */
    public function getMapData(Request $request)
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $query = VisitTracking::whereHas('lead', function ($q) use ($business_id) {
            $q->where('business_id', $business_id);
        })
            ->whereNotNull('checkin_latitude')
            ->whereNotNull('checkin_longitude')
            ->with(['lead:id,reference_no,store_name,company_name,contact_name,contact_phone,city,state', 'salesRep:id,first_name,last_name']);

        // Apply filters
        if ($request->has('sales_rep_id') && $request->sales_rep_id != '') {
            $query->where('sales_rep_id', $request->sales_rep_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('start_time', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        $visits = $query->orderBy('start_time', 'desc')
            ->limit(500)
            ->get();

        $mapData = $visits->map(function ($visit) {
            return [
                'id' => $visit->id,
                'visit_reference' => $visit->visit_reference,
                'lead_name' => $visit->lead ? $visit->lead->store_name : 'N/A',
                'lead_reference' => $visit->lead ? $visit->lead->reference_no : 'N/A',
                'sales_rep' => $visit->salesRep ? trim($visit->salesRep->first_name . ' ' . $visit->salesRep->last_name) : 'N/A',
                'latitude' => (float) $visit->checkin_latitude,
                'longitude' => (float) $visit->checkin_longitude,
                'start_time' => $visit->start_time ? $visit->start_time->format('M d, Y h:i A') : 'N/A',
                'duration' => $visit->duration ?? 'N/A',
                'status' => $visit->status,
                'visit_type' => $visit->visit_type
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $mapData
        ]);
    }
}

