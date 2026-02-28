<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\ShippingStation;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ShippingStationController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @param BusinessUtil $businessUtil
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(Util $commonUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        // Check if this is a request for selection (returns JSON)
        if ($request->has('for_selection')) {
            $business_id = $request->session()->get('user.business_id');
            
            $stations = ShippingStation::where('business_id', $business_id)
                ->where('is_active', 1) // Only active stations
                ->with(['user:id,first_name,last_name'])
                ->select('id', 'name', 'station_code', 'user_id', 'location_id', 'is_active', 'description', 'equipment_notes')
                ->get();

            // Filter by location if user doesn't have access to all locations
            if (!$is_admin) {
                $permitted_locations = auth()->user()->permitted_locations();
                if ($permitted_locations != 'all') {
                    $stations = $stations->filter(function($station) use ($permitted_locations) {
                        return !$station->location_id || in_array($station->location_id, $permitted_locations);
                    });
                }
            }

            // Get order's shipping station ID if order_id is provided
            $orderShippingStationId = null;
            if ($request->has('order_id')) {
                $transaction = \App\Transaction::where('business_id', $business_id)
                    ->where('id', $request->order_id)
                    ->select('shipping_station_id')
                    ->first();
                if ($transaction && $transaction->shipping_station_id) {
                    $orderShippingStationId = $transaction->shipping_station_id;
                }
            }

            return response()->json([
                'success' => true,
                'order_shipping_station_id' => $orderShippingStationId,
                'stations' => $stations->map(function($station) {
                    return [
                        'id' => $station->id,
                        'name' => $station->name,
                        'station_code' => $station->station_code,
                        'description' => $station->description,
                        'equipment_notes' => $station->equipment_notes,
                        'is_active' => $station->is_active,
                        'user' => $station->user ? [
                            'id' => $station->user->id,
                            'first_name' => $station->user->first_name,
                            'last_name' => $station->user->last_name,
                        ] : null,
                    ];
                }),
            ]);
        }

        // Check if this is a DataTables AJAX request (not a modal request)
        if ($request->ajax() && !$request->has('modal')) {
            $business_id = $request->session()->get('user.business_id');
            
            $stations = ShippingStation::where('business_id', $business_id)
                ->with(['businessLocation:id,name', 'business:id,name', 'user:id,first_name,last_name'])
                ->select('shipping_stations.*');

            // Filter by location if user doesn't have access to all locations
            if (!$is_admin) {
                $permitted_locations = auth()->user()->permitted_locations();
                if ($permitted_locations != 'all') {
                    $stations->whereIn('location_id', $permitted_locations);
                }
            }

            return DataTables::of($stations)
                ->addColumn('location_name', function ($row) {
                    return $row->businessLocation ? $row->businessLocation->name : __('lang_v1.none');
                })
                ->addColumn('assigned_user', function ($row) {
                    if ($row->user) {
                        return $row->user->first_name . ' ' . $row->user->last_name;
                    }
                    return __('lang_v1.none');
                })
                ->addColumn('status', function ($row) {
                    if ($row->is_active) {
                        return '<span class="label label-success">' . __('lang_v1.active') . '</span>';
                    } else {
                        return '<span class="label label-danger">' . __('lang_v1.inactive') . '</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    
                    $html .= '<button type="button" class="btn btn-xs btn-info btn-modal" data-href="' . 
                        action([\App\Http\Controllers\ShippingStationController::class, 'show'], [$row->id]) . 
                        '" data-container=".shipping_station_modal" title="' . __("messages.view") . '">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                              </button>';
                    
                    if (auth()->user()->can('brand.update') || $this->businessUtil->is_admin(auth()->user())) {
                        $html .= '<button type="button" class="btn btn-xs btn-primary btn-modal" data-href="' . 
                            action([\App\Http\Controllers\ShippingStationController::class, 'edit'], [$row->id]) . 
                            '" data-container=".shipping_station_modal" title="' . __("messages.edit") . '">
                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                  </button>';
                    }
                    
                    if (auth()->user()->can('brand.delete') || $this->businessUtil->is_admin(auth()->user())) {
                        $html .= '<button data-href="' . 
                            action([\App\Http\Controllers\ShippingStationController::class, 'destroy'], [$row->id]) . 
                            '" class="btn btn-xs btn-danger delete_shipping_station_button" title="' . __("messages.delete") . '">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                  </button>';
                    }
                    
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('shipping_station.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $business_locations = [];
        if (auth()->user()->can('access_all_locations') || $is_admin) {
            $business_locations = BusinessLocation::forDropdown($business_id);
        } else {
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $business_locations = BusinessLocation::where('business_id', $business_id)
                    ->whereIn('id', $permitted_locations)
                    ->select(DB::raw("IF(location_id IS NULL OR location_id='', name, CONCAT(name, ' (', location_id, ')')) AS name"), 'id')
                    ->pluck('name', 'id');
            }
        }

        // Get users for dropdown
        $users = User::forDropdown($business_id, true, false, false, false);

        return view('shipping_station.create')
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
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'location_id' => 'nullable|exists:business_locations,id',
                'station_code' => 'nullable|string|max:50|unique:shipping_stations,station_code',
                'description' => 'nullable|string|max:1000',
                'equipment_notes' => 'nullable|string|max:1000',
                'printer_name' => 'nullable|string|max:255',
                'user_id' => 'nullable|exists:users,id',
                'is_active' => 'nullable|boolean',
            ]);

            $business_id = $request->session()->get('user.business_id');
            
            $input = $request->only([
                'name', 
                'location_id', 
                'station_code', 
                'description', 
                'equipment_notes', 
                'printer_name',
                'user_id'
            ]);
            
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            $input['is_active'] = $request->has('is_active') ? 1 : 0;

            // If location_id is not provided and user doesn't have access to all locations, use first permitted location
            if (empty($input['location_id'])) {
                if (!$is_admin) {
                    $permitted_locations = auth()->user()->permitted_locations();
                    if ($permitted_locations != 'all' && !empty($permitted_locations)) {
                        $input['location_id'] = $permitted_locations[0];
                    }
                }
            }

            $station = ShippingStation::create($input);

            $output = [
                'success' => true,
                'data' => $station,
                'msg' => __('lang_v1.added_success'),
            ];
        } catch (\Exception $e) {
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
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $station = ShippingStation::where('business_id', $business_id)
                ->with(['businessLocation', 'business', 'user'])
                ->findOrFail($id);

            return view('shipping_station.show')
                ->with(compact('station'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $station = ShippingStation::where('business_id', $business_id)->findOrFail($id);

            $business_locations = [];
            if (auth()->user()->can('access_all_locations') || $is_admin) {
                $business_locations = BusinessLocation::forDropdown($business_id);
            } else {
                $permitted_locations = auth()->user()->permitted_locations();
                if ($permitted_locations != 'all') {
                    $business_locations = BusinessLocation::where('business_id', $business_id)
                        ->whereIn('id', $permitted_locations)
                        ->select(DB::raw("IF(location_id IS NULL OR location_id='', name, CONCAT(name, ' (', location_id, ')')) AS name"), 'id')
                        ->pluck('name', 'id');
                }
            }

            // Get users for dropdown
            $users = User::forDropdown($business_id, true, false, false, false);

            return view('shipping_station.edit')
                ->with(compact('station', 'business_locations', 'users'));
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
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'location_id' => 'nullable|exists:business_locations,id',
                    'station_code' => 'nullable|string|max:50|unique:shipping_stations,station_code,' . $id,
                    'description' => 'nullable|string|max:1000',
                    'equipment_notes' => 'nullable|string|max:1000',
                    'printer_name' => 'nullable|string|max:255',
                    'user_id' => 'nullable|exists:users,id',
                    'is_active' => 'nullable|boolean',
                ]);

                $business_id = $request->session()->get('user.business_id');
                $station = ShippingStation::where('business_id', $business_id)->findOrFail($id);

                $input = $request->only([
                    'name', 
                    'location_id', 
                    'station_code', 
                    'description', 
                    'equipment_notes', 
                    'printer_name',
                    'user_id'
                ]);
                
                $input['is_active'] = $request->has('is_active') ? 1 : 0;

                $station->update($input);

                return response()->json([
                    'success' => true, 
                    'msg' => __('lang_v1.updated_success')
                ]);
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                return response()->json([
                    'success' => false, 
                    'msg' => __('messages.something_went_wrong')
                ]);
            }
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
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $station = ShippingStation::where('business_id', $business_id)->findOrFail($id);
                $station->delete();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.deleted_success'),
                ];
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
     * Show form for quick adding station name
     *
     * @return \Illuminate\Http\Response
     */
    public function quickAddStationName()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            return view('shipping_station.quick_add_station_name');
        }
    }

    /**
     * Store quick added station name
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeQuickAddStationName(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            return response()->json([
                'success' => false,
                'msg' => __('Unauthorized action.'),
            ], 403);
        }

        if ($request->ajax()) {
            try {
                $request->validate([
                    'name' => 'required|string|max:255',
                ]);

                $business_id = $request->session()->get('user.business_id');
                $name = trim($request->input('name'));
                
                if (empty($name)) {
                    return response()->json([
                        'success' => false,
                        'msg' => __('Station name is required'),
                    ]);
                }
                
                // Check if name already exists
                $existing = ShippingStation::where('business_id', $business_id)
                    ->where('name', $name)
                    ->exists();
                
                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'msg' => __('Station name already exists'),
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'name' => $name,
                    'msg' => __('lang_v1.added_success'),
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'msg' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    /**
     * Save shipping station to transaction
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveStationToTransaction(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            return response()->json([
                'success' => false,
                'msg' => __('Unauthorized action.'),
            ], 403);
        }

        try {
            $request->validate([
                'transaction_id' => 'required|exists:transactions,id',
                'shipping_station_id' => 'required|exists:shipping_stations,id',
            ]);

            $business_id = $request->session()->get('user.business_id');
            
            // Verify transaction belongs to business
            $transaction = \App\Transaction::where('business_id', $business_id)
                ->findOrFail($request->transaction_id);

            // Verify shipping station belongs to business
            $station = ShippingStation::where('business_id', $business_id)
                ->findOrFail($request->shipping_station_id);

            // Update transaction with shipping station
            $transaction->shipping_station_id = $request->shipping_station_id;
            $transaction->save();

            return response()->json([
                'success' => true,
                'msg' => __('Shipping station assigned successfully'),
                'station' => [
                    'id' => $station->id,
                    'name' => $station->name,
                    'station_code' => $station->station_code,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}

