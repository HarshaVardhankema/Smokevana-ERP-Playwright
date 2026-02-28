<?php

namespace App\Http\Controllers;

use App\LocationTaxType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LocationTaxTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // For now, allow access to all users to debug the data visibility issue
        // TODO: Implement proper permission checks once the basic functionality works
        if (!auth()->user()->can('tax_rate.view') && !auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $locationTaxTypes = LocationTaxType::select(['id', 'name', 'created_at', 'updated_at']);
                
                // Debug: Log the count of records
                Log::info('LocationTaxType count: ' . $locationTaxTypes->count());
                
                // Debug: Log the raw data
                $rawData = $locationTaxTypes->get();
                Log::info('LocationTaxType raw data: ' . $rawData->toJson());
                
                $datatable = DataTables::of($locationTaxTypes)
                    ->addColumn('action', function ($row) {
                        
$html = '<div class="tw-flex tw-gap-2" style="gap:6px;flex-wrap:wrap;">';   

                        
$html .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs table-action-btn table-action-btn-edit btn-modal" data-href="' . route('locationtaxtype.edit', $row->id) . '" data-container=".location_tax_type_modal"><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</button>';
 $html .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs table-action-btn table-action-btn-delete delete-location-tax-type" data-href="' . route('locationtaxtype.destroy', $row->id) . '"><i class="glyphicon glyphicon-trash"></i> ' . __('messages.delete') . '</button>';

                        $html .= '</div>';
                        return $html;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
                    })
                    ->editColumn('updated_at', function ($row) {
                        return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '';
                    })
                    ->rawColumns(['action']);
                
                // Debug: Log the response
                $response = $datatable->make(true);
                Log::info('DataTables response: ' . $response->getContent());
                
                return $response;
            } catch (\Exception $e) {
                Log::error('DataTables error: ' . $e->getMessage());
                Log::error('DataTables error trace: ' . $e->getTraceAsString());
                
                return response()->json([
                    'error' => $e->getMessage(),
                    'data' => [],
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ]);
            }
        }
        
        return view('locationtaxtype.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }
        return view('locationtaxtype.create');
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

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:location_tax_types,name',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        LocationTaxType::create([
            'name' => $request->name,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Location Tax Type created successfully.')]);
        }

        return redirect()->route('locationtaxtype.index')->with('success', __('Location Tax Type created successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }
        $locationTaxType = LocationTaxType::findOrFail($id);
        return view('locationtaxtype.edit', compact('locationTaxType'));
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

        $locationTaxType = LocationTaxType::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:location_tax_types,name,' . $id,
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $locationTaxType->update([
            'name' => $request->name,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Location Tax Type updated successfully.')]);
        }

        return redirect()->route('locationtaxtype.index')->with('success', __('Location Tax Type updated successfully.'));
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
        
        $locationTaxType = LocationTaxType::findOrFail($id);
        $locationTaxType->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'msg' => __('Location Tax Type deleted successfully.')]);
        }
        
        return redirect()->route('locationtaxtype.index')->with('success', __('Location Tax Type deleted successfully.'));
    }
} 