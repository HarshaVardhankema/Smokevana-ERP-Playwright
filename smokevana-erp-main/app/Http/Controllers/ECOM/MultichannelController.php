<?php

namespace App\Http\Controllers\ECOM;

use App\Multichannel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class MultichannelController extends Controller
{
    public function index()
    {
        return redirect()->action([MultichannelController::class, 'multichannel']);
    }

    public function create()
    {
        return view('multi_channel.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|max:255',
                'visibility' => 'required|boolean',
                'status' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'url' => 'required|string|max:500',
                'thumbnail_url' => 'nullable|string|max:500',
                'short_meta' => 'nullable|string|max:1000',
                'meta_data' => 'nullable|array',
            ]);

            $input = $request->only(['type', 'visibility', 'status', 'title', 'url', 'thumbnail_url', 'short_meta']);
            $input['created_by'] = Auth::id();

            // Handle meta_data - already formatted as object from frontend
            if ($request->has('meta_data') && is_array($request->meta_data)) {
                $input['meta_data'] = json_encode($request->meta_data);
            }

            $multichannel = Multichannel::create($input);
            
            $output = [
                'success' => true,
                'data' => $multichannel,
                'msg' => 'Multi Channel created successfully!'
            ];
        } catch (\Exception $e) {
            Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => false,
                'msg' => 'Failed to create Multi Channel: ' . $e->getMessage()
            ];
        }

        return $output;
    }

    public function multichannel()
    {
        if (request()->ajax()) {
            $data = Multichannel::select('id', 'type', 'visibility', 'status', 'title', 'url', 'thumbnail_url', 'short_meta', 'created_at', 'updated_at');
            
            return DataTables::of($data)
                ->addColumn('type', function($row) {
                    return $row->type;
                })
                ->addColumn('status', function($row) {
                    return $row->status;
                })
                ->addColumn('visibility', function($row) {
                    return $row->visibility ? 'Public' : 'Private';
                })
                ->addColumn('title', function($row) {
                    return $row->title;
                })
                ->addColumn('url', function($row) {
                    return $row->url;
                })
                ->addColumn('thumbnail', function($row) {
                    return $row->thumbnail_url ? '<img src="' . $row->thumbnail_url . '" alt="Thumbnail" width="50">' : 'No Image';
                })
                ->addColumn('short_meta', function($row) {
                    return $row->short_meta ? Str::limit($row->short_meta, 50) : '';
                })
                ->addColumn('action', function($row) {
                    return '<button data-href="' . action([MultichannelController::class, 'edit'], [$row->id]) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary edit_multichannel_button"><i class="glyphicon glyphicon-edit"></i></button>
                        &nbsp;
                        <button data-href="' . $row->url . '" target="_blank" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-info view_multichannel_button"><i class="glyphicon glyphicon-eye-open"></i></button>';
                })
                ->rawColumns(['thumbnail', 'action'])
                ->make(true);
        }
        
        return view('multi_channel.index');
    }

    public function edit($id)
    {
        try {
            $multichannel = Multichannel::findOrFail($id);
            
            if (request()->ajax()) {
                return view('multi_channel.edit', compact('multichannel'));
            }
            
            return view('multi_channel.edit', compact('multichannel'));
        } catch (\Exception $e) {
            Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Multi Channel not found'
                ], 404);
            }
            
            return redirect()->action([MultichannelController::class, 'multichannel'])
                ->with('error', 'Multi Channel not found');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'type' => 'required|string|max:255',
                'visibility' => 'required|boolean',
                'status' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'url' => 'required|string|max:500',
                'thumbnail_url' => 'nullable|string|max:500',
                'short_meta' => 'nullable|string|max:1000',
                'meta_data' => 'nullable|array',
            ]);

            $multichannel = Multichannel::findOrFail($id);
            
            $input = $request->only(['type', 'visibility', 'status', 'title', 'url', 'thumbnail_url', 'short_meta']);
            $input['updated_by'] = Auth::id();

            // Handle meta_data - already formatted as object from frontend
            if ($request->has('meta_data') && is_array($request->meta_data)) {
                $input['meta_data'] = json_encode($request->meta_data);
            }

            $multichannel->update($input);
            
            $output = [
                'success' => true,
                'data' => $multichannel,
                'msg' => 'Multi Channel updated successfully!'
            ];
        } catch (\Exception $e) {
            Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => false,
                'msg' => 'Failed to update Multi Channel: ' . $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * API: Get all multi-channels
     */
    public function apiIndex(Request $request)
    {
        try {
            $type = $request->query('type');
            $status = $request->query('status');
            $visibility = $request->query('visibility');
            $title = $request->query('title');

            $query = Multichannel::query();

            if ($type) {
                $query->where('type', $type);
            }
            if ($status) {
                $query->where('status', $status);
            }
            if ($visibility) {
                $query->where('visibility', $visibility);
            }
            if ($title) {
                $query->where('title', $title);
            }
            $multichannels = $query->get();
            // Decode meta_data for each item
            $multichannels->transform(function ($item) {
                if ($item->meta_data) {
                    $item->meta_data = json_decode($item->meta_data, true);
                }
                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $multichannels,
                'msg' => 'Multi Channels retrieved successfully!'
            ]);
        } catch (\Exception $e) {
            Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => 'Failed to retrieve Multi Channels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update multi-channel
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            $request->validate([
                'type' => 'required|string|max:255',
                'visibility' => 'required|boolean',
                'status' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'url' => 'required|string|max:500',
                'thumbnail_url' => 'nullable|string|max:500',
                'short_meta' => 'nullable|string|max:1000',
                'meta_data' => 'nullable|array',
            ]);

            $multichannel = Multichannel::findOrFail($id);
            
            $input = $request->only(['type', 'visibility', 'status', 'title', 'url', 'thumbnail_url', 'short_meta']);
            $input['updated_by'] = Auth::id();

            // Handle meta_data - already formatted as object from frontend
            if ($request->has('meta_data') && is_array($request->meta_data)) {
                $input['meta_data'] = json_encode($request->meta_data);
            }

            $multichannel->update($input);
            
            // Decode meta_data for response
            if ($multichannel->meta_data) {
                $multichannel->meta_data = json_decode($multichannel->meta_data, true);
            }

            return response()->json([
                'success' => true,
                'data' => $multichannel,
                'msg' => 'Multi Channel updated successfully!'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Multi Channel not found'
            ], 404);
        } catch (\Exception $e) {
            Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => 'Failed to update Multi Channel: ' . $e->getMessage()
            ], 500);
        }
    }  
}
