<?php

namespace App\Http\Controllers;

use App\Brands;
use App\TaxRate;
use App\GroupSubTax;
use App\Utils\TaxUtil;
use App\LocationTaxType;
use App\BusinessLocation;
use App\LocationTaxCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TaxRateController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $taxUtil;

    /**
     * Constructor
     *
     * @param  TaxUtil  $taxUtil
     * @return void
     */
    public function __construct(TaxUtil $taxUtil)
    {
        $this->taxUtil = $taxUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('tax_rate.view') && !auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            
            // Get user's location and check if it's B2C
            $user_location_id = $this->getLocationForContact(request());
            $is_b2c = false;
            
            if ($user_location_id) {
                $is_b2c = BusinessLocation::where('id', $user_location_id)->value('is_b2c');
            }

            // Build the query with joins
            $query = LocationTaxCharge::join('location_tax_types', 'location_tax_charges.location_id', '=', 'location_tax_types.id')
                ->leftJoin('business_locations', 'location_tax_charges.web_location_id', '=', 'business_locations.id')
                ->leftJoin('brands', 'location_tax_charges.brand_id', '=', 'brands.id')
                ->select([
                    'location_tax_charges.id',
                    'location_tax_types.name as location_tax_type_name',
                    'location_tax_charges.state_code',
                    'location_tax_charges.tax_type',
                    'location_tax_charges.value',
                    'business_locations.name as business_location_name',
                    'brands.name as brand_name'
                    
                   
                ]);
                                
                if (request()->has('location_filter') && request()->location_filter) {
                    $query->where('location_tax_charges.web_location_id', request()->location_filter);
                }

                if (request()->has('brand_filter') && request()->brand_filter) {
                    $query->where('location_tax_charges.brand_id', request()->brand_filter);
                }

                if (request()->has('tax_type_filter') && request()->tax_type_filter) {
                    $query->where('location_tax_charges.tax_type', request()->tax_type_filter);
                }

            // Filter based on user permissions and B2B/B2C status
            if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
                // Super admin can see all records
                $tax_charges = $query->get();
            } else {
                // Regular users - filter based on their location and B2B/B2C status
                if ($user_location_id) {
                    if ($is_b2c == 1) {
                        // B2C users see only B2C records for their location
                        $tax_charges = $query->where('location_tax_charges.web_location_id', $user_location_id)
                            ->where('business_locations.is_b2c', 1)
                            ->get();
                    } else {
                        // B2B users see only B2B records for their location
                        $tax_charges = $query->where('location_tax_charges.web_location_id', $user_location_id)
                            ->where('business_locations.is_b2c', 0)
                            ->get();
                    }
                } else {
                    // No location assigned, show empty result
                    $tax_charges = collect();
                }
            }

            $can_update = auth()->user()->can('tax_rate.update');
            $can_delete = auth()->user()->can('tax_rate.delete');

            return Datatables::of($tax_charges)
                ->addColumn(
                    'action',
                    function ($row) use ($can_update, $can_delete) {
                        $buttons = '';
                        if ($can_update) {
                            $buttons .= '<button data-href="' . action([\App\Http\Controllers\TaxRateController::class, 'edit'], [$row->id]) . '" class="tw-dw-btn tw-dw-btn-xs table-action-btn table-action-btn-edit edit_tax_rate_button"><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</button>';
                        }
                        if ($can_delete) {
                            $buttons .= ' <button data-href="' . action([\App\Http\Controllers\TaxRateController::class, 'destroy'], [$row->id]) . '" class="tw-dw-btn tw-dw-btn-xs table-action-btn table-action-btn-delete delete_tax_rate_button"><i class="glyphicon glyphicon-trash"></i> ' . __('messages.delete') . '</button>';
                        }
                        return '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">' . $buttons . '</div>';
                    }
                )
                ->addColumn('business_location', function($row) {
                    return $row->business_location_name ?? 'N/A';
                })
                ->addColumn('location_type', function($row) {
                    return $row->is_b2c == 1 ? 'B2C' : 'B2B';
                })
                ->editColumn('value', '{{@num_format($value)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
          // Get business locations for the dropdown
          $location_id = $this->getLocationId(request());
          $business_id = request()->session()->get('user.business_id');
          $business_locations = [];
          if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
              $business_locations = BusinessLocation::forDropdown($business_id,false, false, $location_id);
          }
          $brands = [];
          if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
              $brands = Brands::forDropdown($business_id);
          } else {
              // For B2C users, get brands based on their location
              $user_location_id = $this->getLocationForContact(request());
              if ($user_location_id) {
                  $brands = Brands::forDropdown($business_id, false, false, $user_location_id);
              }
          }
          
          // Get tax types for the filter dropdown
          //$tax_types = LocationTaxCharge::pluck('tax_type', 'id')->toArray();
          $tax_types = LocationTaxCharge::select('tax_type')
                ->distinct()->pluck('tax_type', 'tax_type') // key and value both = tax_type
            ->toArray();
          
          
          return view('tax_rate.index', compact('business_locations', 'brands', 'tax_types'));
  
    }

    public function getLocationId($request)
    {
        $user = auth()->user();
        $business_id = $request->session()->get('user.business_id');
        
        // Check if user is super admin or has access to all locations
        $is_super_admin = $user->can('access_all_locations') || $user->can('admin');
        
        if ($is_super_admin) {
            return null;
        }
    }

    // public function index()
    // {
    //     if (! auth()->user()->can('tax_rate.view') && ! auth()->user()->can('tax_rate.create')) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     if (request()->ajax()) {
    //         $business_id = request()->session()->get('user.business_id');

    //         $tax_rates = TaxRate::where('business_id', $business_id)
    //                     ->where('is_tax_group', '0')
    //                     ->select(['name', 'amount', 'id', 'for_tax_group']);

    //         return Datatables::of($tax_rates)
    //             ->addColumn(
    //                 'action',
    //                 '@can("tax_rate.update")
    //                 <button data-href="{{action(\'App\Http\Controllers\TaxRateController@edit\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary edit_tax_rate_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
    //                     &nbsp;
    //                 @endcan
    //                 @can("tax_rate.delete")
    //                     <button data-href="{{action(\'App\Http\Controllers\TaxRateController@destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_tax_rate_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
    //                 @endcan'
    //             )
    //             ->editColumn('name', '@if($for_tax_group == 1) {{$name}} <small>(@lang("lang_v1.for_tax_group_only"))</small> @else {{$name}} @endif')
    //             ->editColumn('amount', '{{@num_format($amount)}}')
    //             ->removeColumn('for_tax_group')
    //             ->removeColumn('id')
    //             ->rawColumns([0, 2])
    //             ->make(false);
    //     }

    //     return view('tax_rate.index');
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
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
        
        
        if (! auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        $locationTaxTypes = LocationTaxType::pluck('name', 'id')->toArray();

        return view('tax_rate.create', compact('locationTaxTypes', 'business_locations', 'brands', 'is_b2c'));
        // return view('tax_rate.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['location_tax_type', 'tax_type', 'state_name', 'state_code', 'value', 'location_id', 'brand_id']);
            $input['location_id'] = $this->getLocationForContact($request);
            $input['brand_id'] = $request->input('brand_id');
            // $input['business_id'] = $request->session()->get('user.business_id');
            // $input['created_by'] = $request->session()->get('user.id');
            // $input['amount'] = $this->taxUtil->num_uf($input['amount']);
            // $input['for_tax_group'] = ! empty($request->for_tax_group) ? 1 : 0;

            // $tax_rate = TaxRate::create($input);
            // $request->validate([
            //     'location_tax_type' => 'required|exists:location_tax_types,id',
            //     'tax_type' => 'required|string',
            //     'state_name' => 'required|string|max:255',
            //     'state_code' => 'required|string|max:255',
            //     'value' => 'required|numeric',
            // ]);

            // Create a new location tax charge record
            // Log::info($input['location_tax_type'] . '  Location id');
            $tax_rate = LocationTaxCharge::create([
                'location_id' => $input['location_tax_type'], // Store the selected location tax type ID
                'tax_type' => $input['tax_type'],
                'state_name' => $input['state_name'] ?? "NA",
                'state_code' => $input['state_code'],
                'value' => $input['value'],
                'brand_id' => $input['brand_id'],
                'web_location_id' => $input['location_id']

                
            ]);

            // Redirect with success message
            //return redirect()->route('tax_rate.index')->with('success', __('tax_rate.tax_rate_added_success'));
            $output = [
                'success' => true,
                'data' => $input,
                'msg' => __('tax_rate.tax_rate_added_success'),
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
        //
    }
    public function charges(Request $request)
    {
        $permissions = [
            'sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view',
            'view_own_sell_only', 'view_commission_agent_sell',
            'so.view_all', 'so.view_own', 'tax_rate.update'
        ];
        $user = auth()->user();
        $allowed = false;
        foreach ($permissions as $permission) {
            if (method_exists($user, 'can') && $user->can($permission)) {
                $allowed = true;
                break;
            }
        }
        if (! $allowed) {
            abort(403, 'Unauthorized action.');
        }
        $location_id = $request->location_id;
        if(!$location_id){
            $location_id = $this->getLocationForContact($request)??1;
        }
        // if ($request->ajax()) {
            $locationTaxTypes = LocationTaxCharge::join('location_tax_types', 'location_tax_charges.location_id', '=', 'location_tax_types.id')
            ->where('location_tax_charges.web_location_id', $location_id)
                ->select(
                    'location_tax_charges.id',
                    'location_tax_types.name as location_tax_type_name',
                    'location_tax_types.id as location_tax_type_id',
                    'location_tax_charges.state_code',
                    'location_tax_charges.tax_type',
                    'location_tax_charges.value'
                )
                ->get() // Use get() to retrieve the results
                ->toArray(); // Convert to array after getting the results

            return response()->json($locationTaxTypes);
        // }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
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
        }else{
            $tax_rate = LocationTaxCharge::find($id);
            $is_b2c = BusinessLocation::where('id', $tax_rate->web_location_id)->value('is_b2c');
            $brands=Brands::forDropdown($business_id, false, false, $tax_rate->web_location_id);
        }
        if (! auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            // $business_id = request()->session()->get('user.business_id');
            $locationTaxTypes = LocationTaxType::pluck('name', 'id')->toArray();
            $tax_rate = LocationTaxCharge::
                // where('business_id', $business_id)->
                find($id);

            return view('tax_rate.edit')
                ->with(compact('tax_rate', 'locationTaxTypes', 'business_locations', 'brands', 'is_b2c'));
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
        if (! auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                // $input = $request->only(['name', 'amount']);
                $business_id = $request->session()->get('user.business_id');

                // $tax_rate = TaxRate::where('business_id', $business_id)->findOrFail($id);
                // $tax_rate->name = $input['name'];
                // $tax_rate->amount = $this->taxUtil->num_uf($input['amount']);
                // $tax_rate->for_tax_group = ! empty($request->for_tax_group) ? 1 : 0;
                // $tax_rate->save();

                //update group tax amount
                // $group_taxes = GroupSubTax::where('tax_id', $id)
                //     ->get();

                // foreach ($group_taxes as $group_tax) {
                //     $this->taxUtil->updateGroupTaxAmount($group_tax->group_tax_id);
                // }
                $input = $request->only(['location_tax_type', 'tax_type', 'state_name', 'state_code', 'value', 'location_id', 'brand_id']);
                $input['location_id'] = $this->getLocationForContact($request);
                $input['brand_id'] = $request->input('brand_id');
                $tax_rate = LocationTaxCharge::where('id', $id) // First find the record by ID
                    ->update([ // Then use the update method
                        'location_id' => $input['location_tax_type'], // Ensure you're using the correct input field names
                        'tax_type' => $input['tax_type'],
                        'state_name' => $input['state_name'] ?? "NA",
                        'state_code' => $input['state_code'],
                        'value' => $input['value'],
                        'brand_id' => $input['brand_id'],
                        'web_location_id' => $input['location_id']
                        
                    ]);
                $output = [
                    'success' => true,
                    'msg' => __('tax_rate.updated_success'),
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('tax_rate.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                //update group tax amount
                // $group_taxes = GroupSubTax::where('tax_id', $id)
                //     ->get();
                // if ($group_taxes->isEmpty()) {
                //     $business_id = request()->user()->business_id;

                //     $tax_rate = TaxRate::where('business_id', $business_id)->findOrFail($id);
                //     $tax_rate->delete();

                //     $output = [
                //         'success' => true,
                //         'msg' => __('tax_rate.deleted_success'),
                //     ];
                // } else {
                //     $output = [
                //         'success' => false,
                //         'msg' => __('tax_rate.can_not_be_deleted'),
                //     ];
                // }
                $tax_rate = LocationTaxCharge::findOrFail($id);
                $tax_rate->delete();
                $output = [
                    'success' => true,
                    'msg' => __('tax_rate.deleted_success'),
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
}

