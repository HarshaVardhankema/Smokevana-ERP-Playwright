<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            
            $options = Option::select([
                'id',
                'type',
                'key',
                'value',
                'modal_type',
                'modal_id',
                'use_for',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            ]);

            return DataTables::of($options)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group dropdown scroll-safe-dropdown" style="float: right;">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                    data-toggle="dropdown" aria-expanded="false">' .
                                    __("messages.actions") .
                                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu" data-dropdown-align="right" style="right: 0 !important; left: auto !important;">';
                    
                    if (auth()->user()->can('admin')) {
                        $html .= '<li><a href="' . action([\App\Http\Controllers\OptionController::class, 'show'], [$row->id]) . '"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="' . action([\App\Http\Controllers\OptionController::class, 'edit'], [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a data-href="' . action([\App\Http\Controllers\OptionController::class, 'destroy'], [$row->id]) . '" class="delete_option_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                    }
                    
                    $html .= '</ul></div>';
                    
                    return $html;
                })
                ->editColumn('value', function ($row) {
                    $value = strip_tags($row->value);
                    return \Illuminate\Support\Str::limit($value, 50);
                })
                ->editColumn('use_for', function ($row) {
                    return '<span class="label label-' . ($row->use_for == 'frontend' ? 'success' : 'info') . '">' . ucfirst($row->use_for) . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
                })
                ->rawColumns(['action', 'use_for'])
                ->make(true);
        }

        return view('options.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        return view('options.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'type' => 'nullable|string|max:255',
                'key' => 'nullable|string|max:255',
                'value' => 'nullable|string',
                'modal_type' => 'nullable|string|max:255',
                'modal_id' => 'nullable|integer',
                'use_for' => 'required|in:frontend,backend'
            ]);

            $input = $request->only([
                'type',
                'key',
                'value',
                'modal_type',
                'modal_id',
                'use_for'
            ]);
            
            $input['created_by'] = Auth::id();
            $input['updated_by'] = Auth::id();

            Option::create($input);

            $output = [
                'success' => true,
                'msg' => __('Option created successfully')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->route('options.index')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $option = Option::with(['creator', 'updater'])->findOrFail($id);

        return view('options.show', compact('option'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $option = Option::findOrFail($id);

        return view('options.edit', compact('option'));
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
        if (!auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'type' => 'nullable|string|max:255',
                'key' => 'nullable|string|max:255',
                'value' => 'nullable|string',
                'modal_type' => 'nullable|string|max:255',
                'modal_id' => 'nullable|integer',
                'use_for' => 'required|in:frontend,backend'
            ]);

            $option = Option::findOrFail($id);

            $input = $request->only([
                'type',
                'key',
                'value',
                'modal_type',
                'modal_id',
                'use_for'
            ]);
            
            $input['updated_by'] = Auth::id();

            $option->update($input);

            $output = [
                'success' => true,
                'msg' => __('Option updated successfully')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->route('options.index')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $option = Option::findOrFail($id);
                $option->delete();

                $output = [
                    'success' => true,
                    'msg' => __('Option deleted successfully')
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }
    public function getOptionsForFrontend(Request $request){
        try {            // Get options for frontend use
            $options = Option::where('use_for', 'frontend')
                ->select('id', 'type', 'key', 'value', 'modal_type', 'modal_id')
                ->get();
            
            
            return response()->json([
                'success' => true,
                'data' => $options
            ]);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong')
            ], 500);
        }
    }
}
