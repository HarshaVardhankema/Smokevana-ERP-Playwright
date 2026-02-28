<?php

namespace App\Http\Controllers;

use App\Complaint;
use App\Transaction;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('complaint.view') && !auth()->user()->can('complaint.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $complaints = Complaint::where('business_id', $business_id)
                ->with(['creator', 'contact', 'transaction'])
                ->select(['id', 'request_type', 'contact_id', 'transaction_id', 'variation_ids', 'description', 'status', 'created_by', 'created_at']);

            return DataTables::of($complaints)
                ->editColumn('request_type', function ($complaint) {
                    return ucfirst(str_replace('_', ' ', $complaint->request_type));
                })
                ->editColumn('contact_id', function ($complaint) {
                    return $complaint->contact ? $complaint->contact->name : 'N/A';
                })
                ->editColumn('transaction_id', function ($complaint) {
                    return $complaint->transaction ? $complaint->transaction->invoice_no : 'N/A';
                })
                ->addColumn('products', function ($complaint) {
                    if (empty($complaint->variation_ids)) {
                        return 'N/A';
                    }
                    
                    $variations = \App\Variation::with('product')
                        ->whereIn('id', $complaint->variation_ids)
                        ->get();
                    
                    if ($variations->isEmpty()) {
                        return 'N/A';
                    }
                    
                    $productNames = $variations->map(function($variation) {
                        $product = $variation->product;
                        return $product->name . ($variation->name ? ' - ' . $variation->name : '');
                    });
                    
                    return implode('<br>', $productNames->toArray());
                })
                ->editColumn('description', function ($complaint) {
                    return \Illuminate\Support\Str::limit($complaint->description, 50);
                })
                ->editColumn('status', function ($complaint) {
                    $statusColors = [
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        'rejected' => 'danger'
                    ];
                    $color = $statusColors[$complaint->status] ?? 'default';
                    return '<span class="label label-' . $color . '">' . ucfirst($complaint->status) . '</span>';
                })
                ->editColumn('created_by', function ($complaint) {
                    return $complaint->creator ? $complaint->creator->username : 'N/A';
                })
                ->addColumn('action', function ($complaint) {
                    $action = '';
                    if (auth()->user()->can('complaint.view')) {
                        $action .= '<a href="' . action([\App\Http\Controllers\ComplaintController::class, 'show'], [$complaint->id]) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info"><i class="glyphicon glyphicon-eye-open"></i> ' . __("messages.view") . '</a> &nbsp;';
                    }
                    if (auth()->user()->can('complaint.update')) {
                        $action .= '<a href="' . action([\App\Http\Controllers\ComplaintController::class, 'edit'], [$complaint->id]) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a> &nbsp;';
                    }
                    if (auth()->user()->can('complaint.delete')) {
                        $action .= '<button data-href="' . action([\App\Http\Controllers\ComplaintController::class, 'destroy'], [$complaint->id]) . '" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_complaint_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';
                    }
                    return $action;
                })
                ->rawColumns(['action', 'status', 'products'])
                ->make(true);
        }

        return view('complaints.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('complaint.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        // Get contacts for dropdown
        $contacts = \App\Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->select('id', 'name', 'supplier_business_name')
            ->get()
            ->map(function($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name . ($contact->supplier_business_name ? ' - ' . $contact->supplier_business_name : '')
                ];
            })
            ->pluck('name', 'id');

        return view('complaints.create', compact('contacts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('complaint.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'request_type' => 'required|string|max:255',
                'contact_id' => 'nullable|exists:contacts,id',
                'transaction_id' => 'nullable|exists:transactions,id',
                'variation_id' => 'nullable|array', // Accept array of variation IDs
                'variation_id.*' => 'exists:variations,id', // Each ID must exist
                'description' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $business_id = $request->session()->get('user.business_id');
            
            // Handle image uploads
            $attachments = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                    $image->move(public_path('uploads/complaints'), $imageName);
                    $attachments[] = 'uploads/complaints/' . $imageName;
                }
            }
            
            $data = [
                'request_type' => $request->request_type,
                'contact_id' => $request->contact_id,
                'transaction_id' => $request->transaction_id,
                'variation_ids' => $request->variation_id, // Store as array
                'description' => $request->description,
                'attachments' => !empty($attachments) ? $attachments : null,
                'business_id' => $business_id,
                'created_by' => auth()->user()->id,
                'status' => 'pending'
            ];

            DB::beginTransaction();

            $complaint = Complaint::create($data);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('Complaint created successfully')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('complaints')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('complaint.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $complaint = Complaint::where('business_id', $business_id)
            ->with(['creator', 'contact', 'transaction', 'business'])
            ->findOrFail($id);

        return view('complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('complaint.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $complaint = Complaint::where('business_id', $business_id)
            ->with(['contact', 'transaction'])
            ->findOrFail($id);
        
        // Get contacts for dropdown
        $contacts = \App\Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->select('id', 'name', 'supplier_business_name')
            ->get()
            ->map(function($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name . ($contact->supplier_business_name ? ' - ' . $contact->supplier_business_name : '')
                ];
            })
            ->pluck('name', 'id');

        return view('complaints.edit', compact('complaint', 'contacts'));
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
        if (!auth()->user()->can('complaint.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'request_type' => 'required|string|max:255',
                'contact_id' => 'nullable|exists:contacts,id',
                'transaction_id' => 'nullable|exists:transactions,id',
                'variation_id' => 'nullable|array', // Accept array of variation IDs
                'variation_id.*' => 'exists:variations,id', // Each ID must exist
                'description' => 'nullable|string',
                'status' => 'required|in:pending,in_progress,resolved,rejected',
                'admin_response' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $business_id = $request->session()->get('user.business_id');

            $complaint = Complaint::where('business_id', $business_id)->findOrFail($id);

            // Handle new image uploads
            $attachments = $complaint->attachments ?? [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                    $image->move(public_path('uploads/complaints'), $imageName);
                    $attachments[] = 'uploads/complaints/' . $imageName;
                }
            }

            // Handle image deletions
            if ($request->has('delete_images')) {
                $deleteImages = $request->delete_images;
                foreach ($deleteImages as $imageToDelete) {
                    if (($key = array_search($imageToDelete, $attachments)) !== false) {
                        // Delete physical file
                        $filePath = public_path($imageToDelete);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        unset($attachments[$key]);
                    }
                }
                $attachments = array_values($attachments); // Re-index array
            }

            $data = [
                'request_type' => $request->request_type,
                'contact_id' => $request->contact_id,
                'transaction_id' => $request->transaction_id,
                'variation_ids' => $request->variation_id, // Store as array
                'description' => $request->description,
                'status' => $request->status,
                'admin_response' => $request->admin_response,
                'attachments' => !empty($attachments) ? $attachments : null,
            ];

            DB::beginTransaction();

            $complaint->update($data);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('Complaint updated successfully')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('complaints')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('complaint.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $complaint = Complaint::where('business_id', $business_id)->findOrFail($id);

                $complaint->delete();

                $output = [
                    'success' => true,
                    'msg' => __('Complaint deleted successfully')
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    /**
     * Get transactions for a specific contact (AJAX)
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getContactTransactions($contact_id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $transactions = \App\Transaction::where('business_id', $business_id)
            ->where('contact_id', $contact_id)
            ->whereIn('type', ['sell', 'sales_order', 'purchase'])
            ->where('status', 'final')
            ->select('id', 'invoice_no', 'type', 'transaction_date')
            ->orderBy('transaction_date', 'desc')
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'text' => $transaction->invoice_no . ' (' . ucfirst($transaction->type) . ') - ' . date('d M Y', strtotime($transaction->transaction_date))
                ];
            });

        return response()->json($transactions);
    }

    /**
     * Get variations/products for a specific transaction (AJAX)
     *
     * @param  int  $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function getTransactionVariations($transaction_id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $transaction = \App\Transaction::where('business_id', $business_id)
            ->findOrFail($transaction_id);

        $variations = [];

        if ($transaction->type == 'sell' || $transaction->type == 'sales_order') {
            // Get from transaction_sell_lines (both 'sell' and 'sales_order' use this table)
            $sell_lines = \DB::table('transaction_sell_lines as tsl')
                ->join('variations as v', 'tsl.variation_id', '=', 'v.id')
                ->join('products as p', 'v.product_id', '=', 'p.id')
                ->where('tsl.transaction_id', $transaction_id)
                ->select('v.id', 'p.name', 'v.name as variation_name', 'v.sub_sku')
                ->distinct()
                ->get();

            foreach ($sell_lines as $line) {
                $variations[] = [
                    'id' => $line->id,
                    'text' => $line->name . ($line->variation_name ? ' - ' . $line->variation_name : '') . ' (SKU: ' . ($line->sub_sku ?: 'N/A') . ')'
                ];
            }
        } elseif ($transaction->type == 'purchase') {
            // Get from purchase_lines
            $purchase_lines = \DB::table('purchase_lines as pl')
                ->join('variations as v', 'pl.variation_id', '=', 'v.id')
                ->join('products as p', 'v.product_id', '=', 'p.id')
                ->where('pl.transaction_id', $transaction_id)
                ->select('v.id', 'p.name', 'v.name as variation_name', 'v.sub_sku')
                ->distinct()
                ->get();

            foreach ($purchase_lines as $line) {
                $variations[] = [
                    'id' => $line->id,
                    'text' => $line->name . ($line->variation_name ? ' - ' . $line->variation_name : '') . ' (SKU: ' . ($line->sub_sku ?: 'N/A') . ')'
                ];
            }
        }

        return response()->json($variations);
    }

    /**
     * API: Get customer's complaints list
     *
     * @return \Illuminate\Http\Response
     */
    public function apiIndex()
    {
        try {
            // Get authenticated customer
            $contact = \Auth::guard('api')->user();
            
            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get complaints for this customer
            $complaints = Complaint::where('contact_id', $contact->id)
                ->with(['transaction'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($complaint) {
                    // Get all variations for this complaint
                    $products = [];
                    if (!empty($complaint->variation_ids)) {
                        $variations = \App\Variation::with('product')
                            ->whereIn('id', $complaint->variation_ids)
                            ->get();
                        
                        foreach ($variations as $variation) {
                            $products[] = [
                                'id' => $variation->id,
                                'name' => $variation->product->name,
                                'variation' => $variation->name,
                                'sku' => $variation->sub_sku
                            ];
                        }
                    }
                    
                    return [
                        'id' => $complaint->id,
                        'request_type' => $complaint->request_type,
                        'transaction' => $complaint->transaction ? [
                            'id' => $complaint->transaction->id,
                            'invoice_no' => $complaint->transaction->invoice_no,
                            'type' => $complaint->transaction->type,
                            'date' => $complaint->transaction->transaction_date
                        ] : null,
                        'products' => $products, // Multiple products
                        'description' => $complaint->description,
                        'status' => $complaint->status,
                        'admin_response' => $complaint->admin_response,
                        'attachments' => $complaint->attachments,
                        'created_at' => $complaint->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $complaint->updated_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Complaints retrieved successfully',
                'data' => $complaints
            ], 200);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve complaints',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Create a new complaint
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiStore(Request $request)
    {
        try {
            // Get authenticated customer
            $contact = \Auth::guard('api')->user();
            
            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Normalize variation_id to array (handle both array and single value)
            // Laravel automatically parses variation_id[] from multipart/form-data as variation_id array
            $variationIds = $request->variation_id;
            
            // Handle case where it might be a single value instead of array
            if (!is_array($variationIds) && !is_null($variationIds)) {
                // If it's a single value, convert to array
                $variationIds = [$variationIds];
            }
            
            // Convert all values to integers and filter out empty values
            if (is_array($variationIds)) {
                $variationIds = array_map('intval', array_filter($variationIds, function($v) {
                    return $v !== '' && $v !== null;
                }));
                $variationIds = array_values(array_unique($variationIds)); // Re-index array
            }

            // Merge normalized data back into request for validation
            $requestData = $request->all();
            if (is_array($variationIds)) {
                $requestData['variation_id'] = $variationIds;
            }

            // Validation
            $validator = \Validator::make($requestData, [
                'request_type' => 'required|string|max:255',
                'transaction_id' => 'nullable|exists:transactions,id',
                'variation_id' => 'nullable|array', // Accept array of variation IDs
                'variation_id.*' => 'exists:variations,id', // Each ID must exist
                'description' => 'nullable|string|max:5000',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'document' => 'nullable', // Support document field
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If transaction_id provided, validate it belongs to this customer
            if ($request->transaction_id) {
                $transaction = \App\Transaction::where('id', $request->transaction_id)
                    ->where('contact_id', $contact->id)
                    ->first();

                if (!$transaction) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid transaction. The transaction does not belong to you or does not exist.'
                    ], 403);
                }

                // If variation_id provided, validate each variation exists in that transaction
                if (!empty($variationIds) && is_array($variationIds)) {
                    foreach ($variationIds as $variation_id) {
                        $variationExists = false;
                        $variation_id = (int) $variation_id; // Ensure integer
                        
                        if ($transaction->type == 'sell' || $transaction->type == 'sales_order') {
                            // Both 'sell' and 'sales_order' use transaction_sell_lines table
                            $variationExists = \DB::table('transaction_sell_lines')
                                ->where('transaction_id', $transaction->id)
                                ->where('variation_id', $variation_id)
                                ->exists();
                        } elseif ($transaction->type == 'purchase') {
                            $variationExists = \DB::table('purchase_lines')
                                ->where('transaction_id', $transaction->id)
                                ->where('variation_id', $variation_id)
                                ->exists();
                        } else {
                            // If transaction type is not supported, return error
                            return response()->json([
                                'success' => false,
                                'message' => 'Transaction type "' . $transaction->type . '" is not supported for complaints. Only "sell", "sales_order", and "purchase" transactions are allowed.'
                            ], 403);
                        }

                        if (!$variationExists) {
                            // Log for debugging
                            \Log::info('Variation validation failed', [
                                'transaction_id' => $transaction->id,
                                'transaction_type' => $transaction->type,
                                'variation_id' => $variation_id,
                                'contact_id' => $contact->id
                            ]);
                            
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid product (ID: ' . $variation_id . '). One or more products do not exist in the selected transaction.'
                            ], 403);
                        }
                    }
                }
            }

            // Handle image uploads
            $attachments = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                    $image->move(public_path('uploads/complaints'), $imageName);
                    $attachments[] = 'uploads/complaints/' . $imageName;
                }
            }

            // Get business_id from contact
            $business_id = $contact->business_id;

            // Create complaint
            $complaint = Complaint::create([
                'request_type' => $request->request_type,
                'contact_id' => $contact->id,
                'transaction_id' => $request->transaction_id,
                'variation_ids' => !empty($variationIds) ? $variationIds : null, // Store as array (normalized)
                'description' => $request->description,
                'attachments' => !empty($attachments) ? $attachments : null,
                'business_id' => $business_id,
                'created_by' => null, // Customer created, not staff
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Complaint submitted successfully. We will review it and get back to you soon.',
                'data' => [
                    'id' => $complaint->id,
                    'request_type' => $complaint->request_type,
                    'status' => $complaint->status,
                    'created_at' => $complaint->created_at->format('Y-m-d H:i:s')
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit complaint. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Show a specific complaint
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function apiShow($id)
    {
        try {
            // Get authenticated customer
            $contact = \Auth::guard('api')->user();
            
            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get complaint - must belong to this customer
            $complaint = Complaint::where('id', $id)
                ->where('contact_id', $contact->id)
                ->with(['transaction'])
                ->first();

            if (!$complaint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complaint not found or does not belong to you'
                ], 404);
            }

            // Get all variations for this complaint
            $products = [];
            if (!empty($complaint->variation_ids)) {
                $variations = \App\Variation::with('product')
                    ->whereIn('id', $complaint->variation_ids)
                    ->get();
                
                foreach ($variations as $variation) {
                    $products[] = [
                        'id' => $variation->id,
                        'name' => $variation->product->name,
                        'variation' => $variation->name,
                        'sku' => $variation->sub_sku
                    ];
                }
            }

            $data = [
                'id' => $complaint->id,
                'request_type' => $complaint->request_type,
                'transaction' => $complaint->transaction ? [
                    'id' => $complaint->transaction->id,
                    'invoice_no' => $complaint->transaction->invoice_no,
                    'type' => $complaint->transaction->type,
                    'date' => $complaint->transaction->transaction_date
                ] : null,
                'products' => $products, // Multiple products
                'description' => $complaint->description,
                'status' => $complaint->status,
                'admin_response' => $complaint->admin_response,
                'attachments' => $complaint->attachments,
                'created_at' => $complaint->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $complaint->updated_at->format('Y-m-d H:i:s'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Complaint retrieved successfully',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve complaint',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update a complaint (customer can only update pending complaints)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            // Get authenticated customer
            $contact = \Auth::guard('api')->user();
            
            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get complaint - must belong to this customer
            $complaint = Complaint::where('id', $id)
                ->where('contact_id', $contact->id)
                ->first();

            if (!$complaint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complaint not found or does not belong to you'
                ], 404);
            }

            // Customers can only update pending complaints
            if ($complaint->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update complaints that are still pending. This complaint is currently: ' . $complaint->status
                ], 403);
            }

            // Validation
            $validator = \Validator::make($request->all(), [
                'request_type' => 'sometimes|required|string|max:255',
                'transaction_id' => 'nullable|exists:transactions,id',
                'variation_id' => 'nullable|array', // Accept array of variation IDs
                'variation_id.*' => 'exists:variations,id', // Each ID must exist
                'description' => 'nullable|string|max:5000',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If transaction_id provided, validate it belongs to this customer
            if ($request->has('transaction_id') && $request->transaction_id) {
                $transaction = \App\Transaction::where('id', $request->transaction_id)
                    ->where('contact_id', $contact->id)
                    ->first();

                if (!$transaction) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid transaction. The transaction does not belong to you or does not exist.'
                    ], 403);
                }

                // If variation_id provided, validate it exists in that transaction
                if ($request->variation_id) {
                    $variationExists = false;
                    
                    if ($transaction->type == 'sell' || $transaction->type == 'sales_order') {
                        // Both 'sell' and 'sales_order' use transaction_sell_lines table
                        $variationExists = \DB::table('transaction_sell_lines')
                            ->where('transaction_id', $transaction->id)
                            ->where('variation_id', $request->variation_id)
                            ->exists();
                    } elseif ($transaction->type == 'purchase') {
                        $variationExists = \DB::table('purchase_lines')
                            ->where('transaction_id', $transaction->id)
                            ->where('variation_id', $request->variation_id)
                            ->exists();
                    }

                    if (!$variationExists) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid product. The product does not exist in the selected transaction.'
                        ], 403);
                    }
                }
            }

            // Handle new image uploads
            $attachments = $complaint->attachments ?? [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                    $image->move(public_path('uploads/complaints'), $imageName);
                    $attachments[] = 'uploads/complaints/' . $imageName;
                }
            }

            // Update complaint
            $updateData = [];
            if ($request->has('request_type')) {
                $updateData['request_type'] = $request->request_type;
            }
            if ($request->has('transaction_id')) {
                $updateData['transaction_id'] = $request->transaction_id;
            }
            if ($request->has('variation_id')) {
                $updateData['variation_ids'] = $request->variation_id; // Store as array
            }
            if ($request->has('description')) {
                $updateData['description'] = $request->description;
            }
            if ($request->hasFile('images')) {
                $updateData['attachments'] = !empty($attachments) ? $attachments : null;
            }

            $complaint->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Complaint updated successfully',
                'data' => [
                    'id' => $complaint->id,
                    'request_type' => $complaint->request_type,
                    'status' => $complaint->status,
                    'updated_at' => $complaint->updated_at->format('Y-m-d H:i:s')
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update complaint',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get customer's transactions for dropdown
     *
     * @return \Illuminate\Http\Response
     */
    public function apiGetMyTransactions()
    {
        try {
            // Get authenticated customer
            $contact = \Auth::guard('api')->user();
            
            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $transactions = \App\Transaction::where('contact_id', $contact->id)
                ->whereIn('type', ['sell', 'sales_order', 'purchase'])
                ->where('status', 'final')
                ->select('id', 'invoice_no', 'type', 'transaction_date')
                ->orderBy('transaction_date', 'desc')
                ->get()
                ->map(function($transaction) {
                    return [
                        'id' => $transaction->id,
                        'invoice_no' => $transaction->invoice_no,
                        'type' => ucfirst($transaction->type),
                        'date' => date('d M Y', strtotime($transaction->transaction_date))
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Transactions retrieved successfully',
                'data' => $transactions
            ], 200);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transactions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get products from a specific transaction
     *
     * @param  int  $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function apiGetTransactionProducts($transaction_id)
    {
        try {
            // Get authenticated customer
            $contact = \Auth::guard('api')->user();
            
            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Verify transaction belongs to this customer
            $transaction = \App\Transaction::where('id', $transaction_id)
                ->where('contact_id', $contact->id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found or does not belong to you'
                ], 404);
            }

            $products = [];

            if ($transaction->type == 'sell' || $transaction->type == 'sales_order') {
                // Both 'sell' and 'sales_order' use transaction_sell_lines table
                $sell_lines = \DB::table('transaction_sell_lines as tsl')
                    ->join('variations as v', 'tsl.variation_id', '=', 'v.id')
                    ->join('products as p', 'v.product_id', '=', 'p.id')
                    ->where('tsl.transaction_id', $transaction_id)
                    ->select('v.id', 'p.name', 'v.name as variation_name', 'v.sub_sku')
                    ->distinct()
                    ->get();

                foreach ($sell_lines as $line) {
                    $products[] = [
                        'id' => $line->id,
                        'name' => $line->name,
                        'variation' => $line->variation_name,
                        'sku' => $line->sub_sku ?: 'N/A'
                    ];
                }
            } elseif ($transaction->type == 'purchase') {
                $purchase_lines = \DB::table('purchase_lines as pl')
                    ->join('variations as v', 'pl.variation_id', '=', 'v.id')
                    ->join('products as p', 'v.product_id', '=', 'p.id')
                    ->where('pl.transaction_id', $transaction_id)
                    ->select('v.id', 'p.name', 'v.name as variation_name', 'v.sub_sku')
                    ->distinct()
                    ->get();

                foreach ($purchase_lines as $line) {
                    $products[] = [
                        'id' => $line->id,
                        'name' => $line->name,
                        'variation' => $line->variation_name,
                        'sku' => $line->sub_sku ?: 'N/A'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

