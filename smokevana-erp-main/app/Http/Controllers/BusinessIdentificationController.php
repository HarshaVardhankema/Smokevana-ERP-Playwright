<?php

namespace App\Http\Controllers;

use App\BusinessIdentification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BusinessIdentificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('business_identification.view') && !auth()->user()->can('business_identification.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $identifications = BusinessIdentification::with(['creator', 'contact'])
                ->select(['id', 'legal_business_name', 'dba', 'contact_id', 'fein_tax_id', 'status', 'created_by', 'created_at']);

            return DataTables::of($identifications)
                ->editColumn('legal_business_name', function ($identification) {
                    return $identification->legal_business_name . ($identification->dba ? ' (DBA: ' . $identification->dba . ')' : '');
                })
                ->editColumn('contact_id', function ($identification) {
                    return $identification->contact ? $identification->contact->name : 'N/A';
                })
                ->editColumn('status', function ($identification) {
                    $statusColors = [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger'
                    ];
                    $color = $statusColors[$identification->status] ?? 'default';
                    return '<span class="label label-' . $color . '">' . ucfirst($identification->status) . '</span>';
                })
                ->editColumn('created_by', function ($identification) {
                    return $identification->creator ? $identification->creator->username : 'N/A';
                })
                ->editColumn('created_at', function ($identification) {
                    return \Carbon\Carbon::parse($identification->created_at)->format('Y-m-d H:i');
                })
                ->addColumn('action', function ($identification) {
                    $action = '';
                    if (auth()->user()->can('business_identification.view')) {
                        $action .= '<a href="' . action([\App\Http\Controllers\BusinessIdentificationController::class, 'show'], [$identification->id]) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info"><i class="glyphicon glyphicon-eye-open"></i> ' . __("messages.view") . '</a> &nbsp;';
                    }
                    if (auth()->user()->can('business_identification.update')) {
                        $action .= '<a href="' . action([\App\Http\Controllers\BusinessIdentificationController::class, 'edit'], [$identification->id]) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a> &nbsp;';
                    }
                    if (auth()->user()->can('business_identification.delete')) {
                        $action .= '<button data-href="' . action([\App\Http\Controllers\BusinessIdentificationController::class, 'destroy'], [$identification->id]) . '" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_identification_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';
                    }
                    return $action;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('business_identifications.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('business_identification.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get contacts for dropdown
        $contacts = \App\Contact::whereIn('type', ['customer', 'both'])
            ->select('id', 'name', 'supplier_business_name')
            ->get()
            ->map(function($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name . ($contact->supplier_business_name ? ' - ' . $contact->supplier_business_name : '')
                ];
            })
            ->pluck('name', 'id');

        return view('business_identifications.create', compact('contacts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('business_identification.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'legal_business_name' => 'required|string|max:255',
                'dba' => 'nullable|string|max:255',
                'contact_id' => 'required|exists:contacts,id',
                'fein_tax_id' => 'nullable|string|max:50',
                'business_types' => 'nullable|array',
                'business_type_other' => 'nullable|string|max:255',
                'primary_contact_name' => 'nullable|string|max:255',
                'primary_contact_title' => 'nullable|string|max:255',
                'primary_contact_phone' => 'nullable|string|max:50',
                'primary_contact_email' => 'nullable|email|max:255',
                'business_address' => 'nullable|string|max:1000',
                'ship_from_address' => 'nullable|string|max:1000',
                'ship_to_address' => 'nullable|string|max:1000',
                'website_marketplaces' => 'nullable|string|max:1000',
                'resale_certificate_number' => 'nullable|string|max:100',
                'resale_certificate_state' => 'nullable|string|max:100',
                'state_licenses' => 'nullable|array',
                'age_gating_methods' => 'nullable|array',
                'age_gating_other' => 'nullable|string|max:255',
                'prohibited_jurisdictions_acknowledged' => 'nullable|boolean',
                'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max
            ]);
            
            // Handle document uploads
            $attachments = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $documentName = time() . '_' . uniqid() . '.' . $document->extension();
                    $document->move(public_path('uploads/business_identifications'), $documentName);
                    $attachments[] = 'uploads/business_identifications/' . $documentName;
                }
            }
            
            $data = [
                'legal_business_name' => $request->legal_business_name,
                'dba' => $request->dba,
                'contact_id' => $request->contact_id,
                'fein_tax_id' => $request->fein_tax_id,
                'business_types' => $request->business_types,
                'business_type_other' => $request->business_type_other,
                'primary_contact_name' => $request->primary_contact_name,
                'primary_contact_title' => $request->primary_contact_title,
                'primary_contact_phone' => $request->primary_contact_phone,
                'primary_contact_email' => $request->primary_contact_email,
                'business_address' => $request->business_address,
                'ship_from_address' => $request->ship_from_address,
                'ship_to_address' => $request->ship_to_address,
                'website_marketplaces' => $request->website_marketplaces,
                'resale_certificate_number' => $request->resale_certificate_number,
                'resale_certificate_state' => $request->resale_certificate_state,
                'state_licenses' => $request->state_licenses,
                'age_gating_methods' => $request->age_gating_methods,
                'age_gating_other' => $request->age_gating_other,
                'prohibited_jurisdictions_acknowledged' => $request->has('prohibited_jurisdictions_acknowledged') ? true : false,
                'attachments' => !empty($attachments) ? $attachments : null,
                'created_by' => auth()->user()->id,
                'status' => 'pending'
            ];

            DB::beginTransaction();

            $identification = BusinessIdentification::create($data);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => 'Business identification created successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->route('business-identifications.index')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('business_identification.view')) {
            abort(403, 'Unauthorized action.');
        }

        $identification = BusinessIdentification::with(['contact', 'creator'])
            ->findOrFail($id);

        return view('business_identifications.show', compact('identification'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('business_identification.update')) {
            abort(403, 'Unauthorized action.');
        }

        $identification = BusinessIdentification::with(['contact'])
            ->findOrFail($id);
        
        // Get contacts for dropdown
        $contacts = \App\Contact::whereIn('type', ['customer', 'both'])
            ->select('id', 'name', 'supplier_business_name')
            ->get()
            ->map(function($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name . ($contact->supplier_business_name ? ' - ' . $contact->supplier_business_name : '')
                ];
            })
            ->pluck('name', 'id');

        return view('business_identifications.edit', compact('identification', 'contacts'));
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
        if (!auth()->user()->can('business_identification.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'legal_business_name' => 'required|string|max:255',
                'dba' => 'nullable|string|max:255',
                'contact_id' => 'required|exists:contacts,id',
                'fein_tax_id' => 'nullable|string|max:50',
                'business_types' => 'nullable|array',
                'business_type_other' => 'nullable|string|max:255',
                'primary_contact_name' => 'nullable|string|max:255',
                'primary_contact_title' => 'nullable|string|max:255',
                'primary_contact_phone' => 'nullable|string|max:50',
                'primary_contact_email' => 'nullable|email|max:255',
                'business_address' => 'nullable|string|max:1000',
                'ship_from_address' => 'nullable|string|max:1000',
                'ship_to_address' => 'nullable|string|max:1000',
                'website_marketplaces' => 'nullable|string|max:1000',
                'resale_certificate_number' => 'nullable|string|max:100',
                'resale_certificate_state' => 'nullable|string|max:100',
                'state_licenses' => 'nullable|array',
                'age_gating_methods' => 'nullable|array',
                'age_gating_other' => 'nullable|string|max:255',
                'prohibited_jurisdictions_acknowledged' => 'nullable|boolean',
                'status' => 'required|in:pending,approved,rejected',
                'admin_notes' => 'nullable|string',
                'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);

            $identification = BusinessIdentification::findOrFail($id);
            
            // Handle new document uploads
            $attachments = $identification->attachments ?? [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $documentName = time() . '_' . uniqid() . '.' . $document->extension();
                    $document->move(public_path('uploads/business_identifications'), $documentName);
                    $attachments[] = 'uploads/business_identifications/' . $documentName;
                }
            }

            // Handle document removal
            if ($request->has('remove_documents')) {
                $removeDocuments = $request->remove_documents;
                foreach ($removeDocuments as $documentPath) {
                    if (($key = array_search($documentPath, $attachments)) !== false) {
                        unset($attachments[$key]);
                        // Delete the file
                        if (file_exists(public_path($documentPath))) {
                            unlink(public_path($documentPath));
                        }
                    }
                }
                $attachments = array_values($attachments); // Re-index array
            }
            
            $data = [
                'legal_business_name' => $request->legal_business_name,
                'dba' => $request->dba,
                'contact_id' => $request->contact_id,
                'fein_tax_id' => $request->fein_tax_id,
                'business_types' => $request->business_types,
                'business_type_other' => $request->business_type_other,
                'primary_contact_name' => $request->primary_contact_name,
                'primary_contact_title' => $request->primary_contact_title,
                'primary_contact_phone' => $request->primary_contact_phone,
                'primary_contact_email' => $request->primary_contact_email,
                'business_address' => $request->business_address,
                'ship_from_address' => $request->ship_from_address,
                'ship_to_address' => $request->ship_to_address,
                'website_marketplaces' => $request->website_marketplaces,
                'resale_certificate_number' => $request->resale_certificate_number,
                'resale_certificate_state' => $request->resale_certificate_state,
                'state_licenses' => $request->state_licenses,
                'age_gating_methods' => $request->age_gating_methods,
                'age_gating_other' => $request->age_gating_other,
                'prohibited_jurisdictions_acknowledged' => $request->has('prohibited_jurisdictions_acknowledged') ? true : false,
                'attachments' => !empty($attachments) ? $attachments : null,
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
            ];

            DB::beginTransaction();

            $identification->update($data);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => 'Business identification updated successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->route('business-identifications.index')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('business_identification.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $identification = BusinessIdentification::findOrFail($id);

            // Delete associated documents
            if (!empty($identification->attachments)) {
                foreach ($identification->attachments as $documentPath) {
                    if (file_exists(public_path($documentPath))) {
                        unlink(public_path($documentPath));
                    }
                }
            }

            $identification->delete();

            $output = [
                'success' => true,
                'msg' => 'Business identification deleted successfully'
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * API: Get customer's business identifications list
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

            // Get business identifications for this customer
            $identifications = BusinessIdentification::where('contact_id', $contact->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($identification) {
                    return [
                        'id' => $identification->id,
                        'legal_business_name' => $identification->legal_business_name,
                        'dba' => $identification->dba,
                        'fein_tax_id' => $identification->fein_tax_id,
                        'business_types' => $identification->business_types,
                        'business_type_other' => $identification->business_type_other,
                        'primary_contact_name' => $identification->primary_contact_name,
                        'primary_contact_title' => $identification->primary_contact_title,
                        'primary_contact_phone' => $identification->primary_contact_phone,
                        'primary_contact_email' => $identification->primary_contact_email,
                        'business_address' => $identification->business_address,
                        'ship_from_address' => $identification->ship_from_address,
                        'ship_to_address' => $identification->ship_to_address,
                        'website_marketplaces' => $identification->website_marketplaces,
                        'resale_certificate_number' => $identification->resale_certificate_number,
                        'resale_certificate_state' => $identification->resale_certificate_state,
                        'state_licenses' => $identification->state_licenses,
                        'age_gating_methods' => $identification->age_gating_methods,
                        'age_gating_other' => $identification->age_gating_other,
                        'prohibited_jurisdictions_acknowledged' => $identification->prohibited_jurisdictions_acknowledged,
                        'attachments' => $identification->attachments ? array_map(function($path) {
                            return url($path);
                        }, $identification->attachments) : [],
                        'status' => $identification->status,
                        'admin_notes' => $identification->admin_notes,
                        'created_at' => $identification->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $identification->updated_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $identifications
            ]);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching business identifications'
            ], 500);
        }
    }

    /**
     * API: Get single business identification
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

            // Get identification for this customer
            $identification = BusinessIdentification::where('contact_id', $contact->id)
                ->where('id', $id)
                ->first();

            if (!$identification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business identification not found or does not belong to you'
                ], 404);
            }

            $data = [
                'id' => $identification->id,
                'legal_business_name' => $identification->legal_business_name,
                'dba' => $identification->dba,
                'fein_tax_id' => $identification->fein_tax_id,
                'business_types' => $identification->business_types,
                'business_type_other' => $identification->business_type_other,
                'primary_contact_name' => $identification->primary_contact_name,
                'primary_contact_title' => $identification->primary_contact_title,
                'primary_contact_phone' => $identification->primary_contact_phone,
                'primary_contact_email' => $identification->primary_contact_email,
                'business_address' => $identification->business_address,
                'ship_from_address' => $identification->ship_from_address,
                'ship_to_address' => $identification->ship_to_address,
                'website_marketplaces' => $identification->website_marketplaces,
                'resale_certificate_number' => $identification->resale_certificate_number,
                'resale_certificate_state' => $identification->resale_certificate_state,
                'state_licenses' => $identification->state_licenses,
                'age_gating_methods' => $identification->age_gating_methods,
                'age_gating_other' => $identification->age_gating_other,
                'prohibited_jurisdictions_acknowledged' => $identification->prohibited_jurisdictions_acknowledged,
                'attachments' => $identification->attachments ? array_map(function($path) {
                    return url($path);
                }, $identification->attachments) : [],
                'status' => $identification->status,
                'admin_notes' => $identification->admin_notes,
                'created_at' => $identification->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $identification->updated_at->format('Y-m-d H:i:s'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the business identification'
            ], 500);
        }
    }

    /**
     * API: Create a new business identification
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

            // Validation
            $validator = \Validator::make($request->all(), [
                'legal_business_name' => 'required|string|max:255',
                'dba' => 'nullable|string|max:255',
                'fein_tax_id' => 'nullable|string|max:50',
                'business_types' => 'nullable|array',
                'business_type_other' => 'nullable|string|max:255',
                'primary_contact_name' => 'nullable|string|max:255',
                'primary_contact_title' => 'nullable|string|max:255',
                'primary_contact_phone' => 'nullable|string|max:50',
                'primary_contact_email' => 'nullable|email|max:255',
                'business_address' => 'nullable|string|max:1000',
                'ship_from_address' => 'nullable|string|max:1000',
                'ship_to_address' => 'nullable|string|max:1000',
                'website_marketplaces' => 'nullable|string|max:1000',
                'resale_certificate_number' => 'nullable|string|max:100',
                'resale_certificate_state' => 'nullable|string|max:100',
                'state_licenses' => 'nullable|array',
                'age_gating_methods' => 'nullable|array',
                'age_gating_other' => 'nullable|string|max:255',
                'prohibited_jurisdictions_acknowledged' => 'nullable|boolean',
                'documents' => 'nullable|array',
                'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle document uploads
            $attachments = [];
            if ($request->hasFile('documents')) {
                $uploadPath = public_path('uploads/business_identifications');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                foreach ($request->file('documents') as $document) {
                    $documentName = time() . '_' . uniqid() . '.' . $document->extension();
                    $document->move($uploadPath, $documentName);
                    $attachments[] = 'uploads/business_identifications/' . $documentName;
                }
            }

            // Create the business identification
            $data = [
                'legal_business_name' => $request->legal_business_name,
                'dba' => $request->dba,
                'contact_id' => $contact->id,
                'fein_tax_id' => $request->fein_tax_id,
                'business_types' => $request->business_types,
                'business_type_other' => $request->business_type_other,
                'primary_contact_name' => $request->primary_contact_name,
                'primary_contact_title' => $request->primary_contact_title,
                'primary_contact_phone' => $request->primary_contact_phone,
                'primary_contact_email' => $request->primary_contact_email,
                'business_address' => $request->business_address,
                'ship_from_address' => $request->ship_from_address,
                'ship_to_address' => $request->ship_to_address,
                'website_marketplaces' => $request->website_marketplaces,
                'resale_certificate_number' => $request->resale_certificate_number,
                'resale_certificate_state' => $request->resale_certificate_state,
                'state_licenses' => $request->state_licenses,
                'age_gating_methods' => $request->age_gating_methods,
                'age_gating_other' => $request->age_gating_other,
                'prohibited_jurisdictions_acknowledged' => $request->prohibited_jurisdictions_acknowledged ?? false,
                'attachments' => !empty($attachments) ? $attachments : null,
                'status' => 'pending'
            ];

            DB::beginTransaction();
            $identification = BusinessIdentification::create($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Business identification submitted successfully',
                'data' => [
                    'id' => $identification->id,
                    'status' => $identification->status
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the business identification'
            ], 500);
        }
    }

    /**
     * API: Update an existing business identification
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

            // Find the identification and ensure it belongs to this customer
            $identification = BusinessIdentification::where('contact_id', $contact->id)
                ->where('id', $id)
                ->first();

            if (!$identification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business identification not found or does not belong to you'
                ], 404);
            }

            // Only allow updates if status is pending or rejected
            if (!in_array($identification->status, ['pending', 'rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update business identification with status: ' . $identification->status
                ], 403);
            }

            // Validation
            $validator = \Validator::make($request->all(), [
                'legal_business_name' => 'sometimes|required|string|max:255',
                'dba' => 'nullable|string|max:255',
                'fein_tax_id' => 'nullable|string|max:50',
                'business_types' => 'nullable|array',
                'business_type_other' => 'nullable|string|max:255',
                'primary_contact_name' => 'nullable|string|max:255',
                'primary_contact_title' => 'nullable|string|max:255',
                'primary_contact_phone' => 'nullable|string|max:50',
                'primary_contact_email' => 'nullable|email|max:255',
                'business_address' => 'nullable|string|max:1000',
                'ship_from_address' => 'nullable|string|max:1000',
                'ship_to_address' => 'nullable|string|max:1000',
                'website_marketplaces' => 'nullable|string|max:1000',
                'resale_certificate_number' => 'nullable|string|max:100',
                'resale_certificate_state' => 'nullable|string|max:100',
                'state_licenses' => 'nullable|array',
                'age_gating_methods' => 'nullable|array',
                'age_gating_other' => 'nullable|string|max:255',
                'prohibited_jurisdictions_acknowledged' => 'nullable|boolean',
                'documents' => 'nullable|array',
                'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle new document uploads
            $attachments = $identification->attachments ?? [];
            if ($request->hasFile('documents')) {
                $uploadPath = public_path('uploads/business_identifications');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                foreach ($request->file('documents') as $document) {
                    $documentName = time() . '_' . uniqid() . '.' . $document->extension();
                    $document->move($uploadPath, $documentName);
                    $attachments[] = 'uploads/business_identifications/' . $documentName;
                }
            }

            // Update only provided fields
            $data = array_filter([
                'legal_business_name' => $request->legal_business_name,
                'dba' => $request->dba,
                'fein_tax_id' => $request->fein_tax_id,
                'business_types' => $request->business_types,
                'business_type_other' => $request->business_type_other,
                'primary_contact_name' => $request->primary_contact_name,
                'primary_contact_title' => $request->primary_contact_title,
                'primary_contact_phone' => $request->primary_contact_phone,
                'primary_contact_email' => $request->primary_contact_email,
                'business_address' => $request->business_address,
                'ship_from_address' => $request->ship_from_address,
                'ship_to_address' => $request->ship_to_address,
                'website_marketplaces' => $request->website_marketplaces,
                'resale_certificate_number' => $request->resale_certificate_number,
                'resale_certificate_state' => $request->resale_certificate_state,
                'state_licenses' => $request->state_licenses,
                'age_gating_methods' => $request->age_gating_methods,
                'age_gating_other' => $request->age_gating_other,
                'attachments' => !empty($attachments) ? $attachments : $identification->attachments,
            ], function($value) {
                return $value !== null;
            });

            if ($request->has('prohibited_jurisdictions_acknowledged')) {
                $data['prohibited_jurisdictions_acknowledged'] = $request->prohibited_jurisdictions_acknowledged;
            }

            // Reset status to pending if it was rejected and now being updated
            if ($identification->status === 'rejected') {
                $data['status'] = 'pending';
            }

            DB::beginTransaction();
            $identification->update($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Business identification updated successfully',
                'data' => [
                    'id' => $identification->id,
                    'status' => $identification->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the business identification'
            ], 500);
        }
    }
}

