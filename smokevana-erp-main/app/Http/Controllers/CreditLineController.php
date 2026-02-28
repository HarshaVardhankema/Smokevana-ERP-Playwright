<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Jobs\SendNotificationJob;
use App\Models\CreditApplication;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CreditLineController extends Controller
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
     * Check if user has access to B2B locations (matches AdminSidebarMenu logic)
     *
     * @return bool
     */
    private function hasB2BAccess()
    {
        $user = auth()->user();
        $business_id = session('business.id');
        
        if (!$business_id) {
            return false;
        }
        
        $permitted_locations = $user->permitted_locations($business_id);
        
        if ($permitted_locations == 'all') {
            // User has access to all locations, check if any location is B2B
            return BusinessLocation::where('business_id', $business_id)
                ->where('is_b2c', 0)
                ->exists();
        } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
            // User has specific location permissions, check if any is B2B
            return BusinessLocation::whereIn('id', $permitted_locations)
                ->where('is_b2c', 0)
                ->exists();
        }
        
        return false;
    }

    /**
     * Check if user is authorized to access credit lines (matches AdminSidebarMenu logic)
     * Credit lines are only accessible to users with customer.view permission AND B2B location access
     *
     * @return bool
     */
    private function isAuthorized()
    {
        // Check if user has customer.view permission (matches sidebar menu check)
        if (!auth()->user()->can('customer.view')) {
            return false;
        }
        
        // Check for B2B access (credit lines are B2B only)
        return $this->hasB2BAccess();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            $credit_applications = CreditApplication::with(['contact.location'])
                ->whereHas('contact', function($query) use ($business_id) {
                    $query->where('business_id', $business_id)
                          ->whereIn('type', ['customer', 'both']);
                })
                ->get();

            return DataTables::of($credit_applications)
                ->addColumn('customer_name', function ($row) {
                    return $row->contact->name;
                })
                ->addColumn('requested_credit_amount', function ($row) {
                    $amount = $row->requested_credit_amount ?? 0;
                    return '<span class="display_currency" data-original-value="' . $amount . '">' . $this->commonUtil->num_f($amount, true) . '</span>';
                })
                ->addColumn('approved_credit_limit', function ($row) {
                    if ($row->credit_application_status === 'approved' && $row->contact->credit_limit > 0) {
                        return '<span class="label label-success display_currency" data-original-value="' . $row->contact->credit_limit . '">' . $this->commonUtil->num_f($row->contact->credit_limit, true) . '</span>';
                    } else {
                        return '<span class="label label-default">Not Set</span>';
                    }
                })
                ->addColumn('average_revenue_per_month', function ($row) {
                    return '<span class="display_currency" data-original-value="' . $row->average_revenue_per_month . '">' . $this->commonUtil->num_f($row->average_revenue_per_month, true) . '</span>';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->credit_application_status ?? 'pending';
                    $badgeColor = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');
                    return '<span class="label label-' . $badgeColor . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group dropdown scroll-safe-dropdown"><button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-propagate="false" aria-expanded="false">' . __('messages.action') . ' <span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    
                    $status = $row->credit_application_status ?? 'pending';
                    
                    // Always show view button for all applications
                    $html .= '<li><a href="' . route('credit-lines.view', $row->id) . '" class="view_credit_line_button"><i class="fa fa-eye"></i> View Details</a></li>';
                    
                    if ($status === 'pending') {
                        // Show approve and reject buttons for pending applications
                        $html .= '<li><a href="' . route('credit-lines.approve', $row->id) . '" class="approve_credit_line_button"><i class="fas fa-check-square"></i> ' . __('messages.approve') . '</a></li>';
                        $html .= '<li><a href="' . route('credit-lines.reject', $row->id) . '" class="reject_credit_line_button"><i class="fa fa-ban" aria-hidden="true"></i> ' . __('messages.reject') . '</a></li>';
                    } elseif ($status === 'approved') {
                        // Show edit button for approved applications
                        $html .= '<li><a href="' . route('credit-lines.edit', $row->contact_id) . '" class="edit_credit_line_button"><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</a></li>';
                    }
                    
                    $html .= '</ul></div>';
                    return $html;
                })
                ->rawColumns(['requested_credit_amount', 'approved_credit_limit', 'status', 'action', 'average_revenue_per_month'])
                ->make(true);
        }

        return view('credit_line.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            // Get all customers that can apply for credit
            $business_id = auth()->user()->business_id;
            
            // Get contacts that don't have a pending application (approved or rejected customers can apply again)
            $contactsWithPendingApplications = CreditApplication::where('credit_application_status', 'pending')
                ->pluck('contact_id')
                ->toArray();
            
            $contacts = Contact::where('business_id', $business_id)
                ->whereIn('contacts.type', ['customer', 'both'])
                ->where('contacts.contact_status', 'active')
                ->whereNotIn('contacts.id', $contactsWithPendingApplications)
                ->select('contacts.id', 
                    'contacts.credit_limit',
                    DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name), CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', name, ' (', contacts.contact_id, ')')) AS name"))
                ->orderBy('contacts.name')
                ->get();

            return view('credit_line.create', compact('contacts'));
            
        } catch (\Exception $e) {
            Log::error('Failed to load create form: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('credit-lines.index')
                ->with('error', 'Failed to load create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            DB::beginTransaction();

            // Validate required fields
            $request->validate([
                'contact_id' => 'required|exists:contacts,id',
                'requested_credit_amount' => 'required|numeric|min:0',
                'average_revenue_per_month' => 'required|numeric|min:0',
                'supporting_documents' => 'required|array|max:10',
                'supporting_documents.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
                'authorized_signatory_name' => 'required|string|max:255',
                'authorized_signatory_email' => 'required|email|max:255',
                'authorized_signatory_phone' => 'required|string|min:10|max:10',
                'digital_signatures' => 'required|array|max:10',
                'digital_signatures.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240' // 10MB max per file
            ]);

            // Find the contact
            $contact = Contact::findOrFail($request->contact_id);

            // Check if there's a pending application - customer cannot create new request if pending exists
            $pendingApplication = CreditApplication::where('contact_id', $contact->id)
                ->where('credit_application_status', 'pending')
                ->first();

            if($pendingApplication) {
                return redirect()->back()
                    ->withErrors(['contact_id' => 'You already have a pending credit application under review. Please wait for approval or rejection before submitting a new request.'])
                    ->withInput();
            }

            if($contact->credit_limit >= $request->requested_credit_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please request a credit limit greater than the existing credit limit'
                ], 400);
            }

            $supportingDocumentsPaths = [];
            $digitalSignaturesPaths = [];

            // Create directory if it doesn't exist
            $destination_path = public_path('uploads/img');
            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0755, true);
            }

            // Upload supporting documents
            if ($request->hasFile('supporting_documents')) {
                foreach ($request->file('supporting_documents') as $index => $document) {
                    // Generate secure filename to prevent directory traversal
                    $secureFilename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $document->getClientOriginalName());
                    
                    // Get file info before moving (since move deletes the original)
                    $fileSize = $document->getSize();
                    $fileType = $document->getMimeType();
                    $originalName = $document->getClientOriginalName();
                    
                    // Move file directly to public/uploads/img (like Product Controller)
                    $result = $document->move($destination_path, $secureFilename);
                    
                    if ($result) {
                        $file_path = 'uploads/img/' . $secureFilename;
                        
                        $supportingDocumentsPaths[] = [
                            'file_path' => $file_path,
                            'original_name' => $originalName,
                            'secure_filename' => $secureFilename,
                            'file_size' => $fileSize,
                            'file_type' => $fileType,
                            'uploaded_at' => now() 
                        ];
                    }
                }
            }

            // Upload digital signatures
            if ($request->hasFile('digital_signatures')) {
                foreach ($request->file('digital_signatures') as $index => $signature) {
                    // Generate secure filename to prevent directory traversal
                    $secureFilename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $signature->getClientOriginalName());
                    
                    // Get file info before moving (since move deletes the original)
                    $fileSize = $signature->getSize();
                    $fileType = $signature->getMimeType();
                    $originalName = $signature->getClientOriginalName();
                    
                    // Move file directly to public/uploads/img
                    $result = $signature->move($destination_path, $secureFilename);
                    
                    if ($result) {
                        $file_path = 'uploads/img/' . $secureFilename;
                        
                        $digitalSignaturesPaths[] = [
                            'file_path' => $file_path,
                            'original_name' => $originalName,
                            'secure_filename' => $secureFilename,
                            'file_size' => $fileSize,
                            'file_type' => $fileType,
                            'uploaded_at' => now() 
                        ];
                    }
                }
            }

            // Create credit application record
            CreditApplication::create([
                'contact_id' => $contact->id,
                'requested_credit_amount' => $request->requested_credit_amount,
                'average_revenue_per_month' => $request->average_revenue_per_month,
                'supporting_documents_paths' => !empty($supportingDocumentsPaths) ? $supportingDocumentsPaths : null,
                'authorized_signatory_name' => $request->authorized_signatory_name ?? 'Not Provided',
                'authorized_signatory_email' => $request->authorized_signatory_email ?? 'Not Provided',
                'authorized_signatory_phone' => $request->authorized_signatory_phone ?? 'Not Provided',
                'digital_signatures_paths' => !empty($digitalSignaturesPaths) ? $digitalSignaturesPaths : null,
                'credit_application_status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('credit-lines.index')
                ->with('success', 'Credit application submitted successfully for ' . $contact->name . '. It is now pending review.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Failed to store credit application: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withErrors(['general' => 'Failed to submit credit application. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
 * Show detailed view of credit application
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function view($id)
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }
        
        $creditApplication = CreditApplication::with('contact')
            ->whereHas('contact', function($query) {
                $query->where('business_id', session('business.id'));
            })
            ->find($id);
        
        if (!$creditApplication) {
            abort(404, 'Credit application not found.');
        }

        // Pass credit application data to view, maintaining compatibility with existing views
        $contact = $creditApplication->contact;
        $contact->requested_credit_amount = $creditApplication->requested_credit_amount;
        $contact->average_revenue_per_month = $creditApplication->average_revenue_per_month;
        $contact->supporting_documents_paths = $creditApplication->supporting_documents_paths;
        $contact->authorized_signatory_name = $creditApplication->authorized_signatory_name;
        $contact->authorized_signatory_email = $creditApplication->authorized_signatory_email;
        $contact->authorized_signatory_phone = $creditApplication->authorized_signatory_phone;
        $contact->digital_signatures_paths = $creditApplication->digital_signatures_paths;
        $contact->credit_application_status = $creditApplication->credit_application_status;

        // Get all credit applications for this customer to show history
        $allApplications = CreditApplication::where('contact_id', $contact->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('credit_line.view_details', compact('contact', 'creditApplication', 'allApplications'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!$this->isAuthorized() || !auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }
        
        $contact = Contact::where('business_id', session('business.id'))->find($id);
        
        if (!$contact) {
            abort(404, 'Contact not found.');
        }

        // Check if contact has an approved credit application
        $creditApplication = CreditApplication::where('contact_id', $contact->id)
            ->where('credit_application_status', 'approved')
            ->first();

        if (!$creditApplication) {
            abort(404, 'No approved credit application found for this contact.');
        }

        // Get customer groups for dropdown if needed
        $customerGroups = collect();
        
        return view('credit_line.edit', compact('contact', 'customerGroups', 'creditApplication'));
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
        if (!$this->isAuthorized() || !auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = session('business.id');
            
            if (!$business_id) {
                throw new \Exception('Business session not found.');
            }
            
            $contact = Contact::where('business_id', $business_id)->find($id);
            
            if (!$contact) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'msg' => 'Contact not found.'
                    ], 404);
                }
                abort(404, 'Contact not found.');
            }

            // Check if contact has an approved credit application
            $creditApplication = CreditApplication::where('contact_id', $contact->id)
                ->where('credit_application_status', 'approved')
                ->first();

            if (!$creditApplication) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'msg' => 'No approved credit application found.'
                    ], 404);
                }
                abort(404, 'No approved credit application found.');
            }

            // Validate the credit limit update
            $request->validate([
                'credit_limit' => 'required|numeric|min:0.01',
            ], [
                'credit_limit.required' => 'Credit limit is required.',
                'credit_limit.numeric' => 'Credit limit must be a valid number.',
                'credit_limit.min' => 'Credit limit must be greater than 0.'
            ]);

            // Update only the credit limit
            $updateResult = $contact->update([
                'credit_limit' => $request->credit_limit
            ]);
            
            if (!$updateResult) {
                throw new \Exception('Failed to update credit limit.');
            }

            Log::info('Credit limit updated successfully', [
                'contact_id' => $contact->id,
                'old_credit_limit' => $request->old_credit_limit ?? 'unknown',
                'new_credit_limit' => $request->credit_limit,
                'updated_by' => auth()->id()
            ]);

            $successMessage = 'Credit limit updated successfully to ' . number_format($request->credit_limit, 2);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'msg' => $successMessage,
                    'redirect_url' => route('credit-lines.index')
                ]);
            }

            return redirect()->route('credit-lines.index')->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Validation failed: ' . implode(', ', $e->errors()),
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error("Credit limit update error", [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'contact_id' => $id ?? null,
                'business_id' => session('business.id') ?? null,
                'user_id' => auth()->id() ?? null,
                'request_data' => $request->all()
            ]);
            
            $errorMessage = 'Something went wrong while updating the credit limit.';
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'msg' => $errorMessage . (config('app.debug') ? ': ' . $e->getMessage() : '')
                ], 500);
            }
            
            return redirect()->back()->with('error', $errorMessage);
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
    }

    /**
     * Show approval form for credit application
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        if (!$this->isAuthorized() || !auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }
        
        $creditApplication = CreditApplication::with('contact')
            ->whereHas('contact', function($query) {
                $query->where('business_id', session('business.id'));
            })
            ->find($id);
        
        if (!$creditApplication) {
            abort(404, 'Credit application not found.');
        }

        // Pass credit application data to contact object for compatibility with view
        $contact = $creditApplication->contact;
        $contact->requested_credit_amount = $creditApplication->requested_credit_amount;
        $contact->average_revenue_per_month = $creditApplication->average_revenue_per_month;
        $contact->supporting_documents_paths = $creditApplication->supporting_documents_paths;
        $contact->authorized_signatory_name = $creditApplication->authorized_signatory_name;
        $contact->authorized_signatory_email = $creditApplication->authorized_signatory_email;
        $contact->authorized_signatory_phone = $creditApplication->authorized_signatory_phone;
        $contact->digital_signatures_paths = $creditApplication->digital_signatures_paths;
        $contact->credit_application_status = $creditApplication->credit_application_status;

        // Get customer groups for dropdown if needed
        $customerGroups = collect();
        
        return view('credit_line.approve_form', compact('contact', 'customerGroups', 'creditApplication'));
    }

    /**
     * Process approval form submission
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function processApproval(Request $request, $id)
    {
        // Debug logging
        Log::info('Credit approval process started', [
            'user_id' => auth()->id(),
            'contact_id' => $id,
            'business_id' => session('business.id'),
            'request_data' => $request->all()
        ]);

        // Check authentication and permissions
        if (!$this->isAuthorized()) {
            Log::error('Unauthorized access attempt', [
                'user_id' => auth()->id(),
                'business_id' => session('business.id'),
                'has_customer_view' => auth()->user()->can('customer.view'),
                'has_b2b_access' => $this->hasB2BAccess(),
                'user_roles' => auth()->user()->roles->pluck('name')->toArray()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Unauthorized action.'
                ], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = session('business.id');
            
            if (!$business_id) {
                throw new \Exception('Business session not found.');
            }
            
            Log::info('Looking for credit application', ['business_id' => $business_id, 'application_id' => $id]);
            
            $creditApplication = CreditApplication::with('contact')
                ->whereHas('contact', function($query) use ($business_id) {
                    $query->where('business_id', $business_id);
                })
                ->find($id);
            
            if (!$creditApplication) {
                Log::error('Credit application not found', ['business_id' => $business_id, 'application_id' => $id]);
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'msg' => 'Credit application not found.'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Credit application not found.');
            }

            $contact = $creditApplication->contact;
            Log::info('Credit application found', ['contact_name' => $contact->name, 'current_status' => $creditApplication->credit_application_status]);

            // Validate the approval data with custom error messages
            $validator = $request->validate([
                'approve_credit_limit' => 'required|numeric|min:0.01'
            ], [
                'approve_credit_limit.required' => 'Credit limit is required.',
                'approve_credit_limit.numeric' => 'Credit limit must be a valid number.',
                'approve_credit_limit.min' => 'Credit limit must be greater than 0.'
            ]);

            // Additional validation for business existence
            if (!$business_id) {
                throw new \Exception('Business session not found.');
            }


            // Update credit application status
            $creditApplication->update([
                'credit_application_status' => 'approved'
            ]);

            // Update contact with approved credit limit
            $updateResult = $contact->update([
                'credit_limit' => $request->approve_credit_limit
            ]);
            
            if (!$updateResult) {
                throw new \Exception('Failed to update contact credit limit.');
            }

            // Send email notification after successful approval
            try {
                $custom_data = [
                    'credit_limit' => number_format($request->approve_credit_limit, 2),
                    'contact_name' => $contact->name,
                    'requested_credit_amount' => $creditApplication->requested_credit_amount,
                    'business_name' => Business::find($business_id)->name ?? 'Business',
                    'url_business' => url('/'),
                ];
                
                SendNotificationJob::dispatch(
                    true, // is_custom
                    $business_id,
                    'credit_limit_approved',
                    auth()->user(),
                    $contact,
                    null, // transaction
                    $custom_data
                );
                
                Log::info('Credit approval notification dispatched', [
                    'contact_id' => $contact->id,
                    'business_id' => $business_id,
                    'credit_limit' => $request->approve_credit_limit
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the approval process
                Log::error('Failed to dispatch credit approval notification', [
                    'contact_id' => $contact->id,
                    'business_id' => $business_id,
                    'error' => $e->getMessage()
                ]);
            }

            $successMessage = 'Credit application approved successfully. Credit limit set to ' . number_format($request->approve_credit_limit, 2);

            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'msg' => $successMessage,
                    'redirect_url' => route('credit-lines.index')
                ]);
            }

            return redirect()->route('credit-lines.index')->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Validation failed: ' . implode(', ', $e->errors()),
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error("Credit approval error", [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'contact_id' => $id ?? null,
                'business_id' => session('business.id') ?? null,
                'user_id' => auth()->id() ?? null,
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Something went wrong while approving the credit application.';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'msg' => $errorMessage . (config('app.debug') ? ': ' . $e->getMessage() : ''),
                    'debug_message' => config('app.debug') ? $e->getMessage() : null,
                    'debug_file' => config('app.debug') ? $e->getFile() . ':' . $e->getLine() : null
                ], 500);
            }
            
            return redirect()->back()->with('error', $errorMessage . (config('app.debug') ? ': ' . $e->getMessage() : ''));
        }
    }

    /**
     * Reject credit application
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject($id)
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                
                $creditApplication = CreditApplication::with('contact')
                    ->whereHas('contact', function($query) use ($business_id) {
                        $query->where('business_id', $business_id);
                    })
                    ->find($id);
                
                if (!$creditApplication) {
                    return [
                        'success' => false,
                        'msg' => __('messages.something_went_wrong')
                    ];
                }

                $creditApplication->credit_application_status = 'rejected';
                $creditApplication->save();

                $output = [
                    'success' => true,
                    'msg' => __('Credit application rejected successfully'),
                    'application_id' => $id
                ];

                return $output;
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                return [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }
        }
    }

    public function storeCreditApplication(Request $request)
    {
        try {
            // Check if user is authenticated
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                Log::warning('Authentication failed', ['auth_data' => $authData]);
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication required to submit credit application'
                ], 401);
            }

            $contact = $authData['user'];

            // Check if there's a pending application - customer cannot create new request if pending exists
            $pendingApplication = CreditApplication::where('contact_id', $contact->id)
                ->where('credit_application_status', 'pending')
                ->first();
            if($contact->customer_group_id == 1 && 5000 < $request->requested_credit_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please upgrade your customer group to submit a credit application.'
                ], 400);
            }

            if($pendingApplication) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already have a pending credit application under review. Please wait for approval or rejection before submitting a new request.'
                ], 400);
            }
            if($contact->credit_limit >= $request->requested_credit_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please request a credit limit greater than the existing credit limit'
                ], 400);
            }
            
            // Debug info
            Log::info('Starting credit application process', [
                'contact_id' => $contact->id ?? 'null',
                'contact_type' => get_class($contact),
                'has_files' => $request->hasFile('supporting_documents')
            ]);

            // Validate required fields
            $request->validate([
                'requested_credit_amount' => 'required|numeric|min:0',
                'average_revenue_per_month' => 'required|numeric|min:0',
                'supporting_documents' => 'required|array|max:10',
                'supporting_documents.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
                'authorized_signatory_name' => 'required|string|max:255',
                'authorized_signatory_email' => 'required|email|max:255',
                'authorized_signatory_phone' => 'required|string|min:10|max:10',
                'digital_signatures' => 'required|array|max:10',
                'digital_signatures.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240' // 10MB max per file
            ]);

            $supportingDocumentsPaths = [];
            $digitalSignaturesPaths = [];

            // Create directory if it doesn't exist
            $destination_path = public_path('uploads/img');
            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0755, true);
            }

            // Upload supporting documents
            if ($request->hasFile('supporting_documents')) {
                foreach ($request->file('supporting_documents') as $index => $document) {
                    // Generate secure filename to prevent directory traversal
                    $secureFilename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $document->getClientOriginalName());
                    
                    // Get file info before moving (since move deletes the original)
                    $fileSize = $document->getSize();
                    $fileType = $document->getMimeType();
                    $originalName = $document->getClientOriginalName();
                    
                    // Move file directly to public/uploads/img (like Product Controller)
                    $result = $document->move($destination_path, $secureFilename);
                    
                    if ($result) {
                        $file_path = 'uploads/img/' . $secureFilename;
                    }
                    
                    // Log for debugging
                    Log::info('Supporting document uploaded successfully', [
                        'path' => $file_path,
                        'filename' => $secureFilename,
                        'original_name' => $originalName,
                        'result' => $result
                    ]);
                    
                    $supportingDocumentsPaths[] = [
                        'file_path' => $file_path,
                        'original_name' => $originalName,
                        'secure_filename' => $secureFilename,
                        'file_size' => $fileSize,
                        'file_type' => $fileType,
                        'uploaded_at' => now() 
                    ];
                }
            }

            // Upload digital signatures
            if ($request->hasFile('digital_signatures')) {
                foreach ($request->file('digital_signatures') as $index => $signature) {
                    // Generate secure filename to prevent directory traversal
                    $secureFilename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $signature->getClientOriginalName());
                    
                    // Get file info before moving (since move deletes the original)
                    $fileSize = $signature->getSize();
                    $fileType = $signature->getMimeType();
                    $originalName = $signature->getClientOriginalName();
                    
                    // Move file directly to public/uploads/img
                    $result = $signature->move($destination_path, $secureFilename);
                    
                    if ($result) {
                        $file_path = 'uploads/img/' . $secureFilename;
                    }
                    
                    // Log for debugging
                    Log::info('Digital signature uploaded successfully', [
                        'path' => $file_path,
                        'filename' => $secureFilename,
                        'original_name' => $originalName,
                        'result' => $result
                    ]);
                    
                    $digitalSignaturesPaths[] = [
                        'file_path' => $file_path,
                        'original_name' => $originalName,
                        'secure_filename' => $secureFilename,
                        'file_size' => $fileSize,
                        'file_type' => $fileType,
                        'uploaded_at' => now() 
                    ];
                }
            }

            // Create credit application record
            $applicationData = [
                'contact_id' => $contact->id,
                'requested_credit_amount' => $request->requested_credit_amount,
                'average_revenue_per_month' => $request->average_revenue_per_month,
                'supporting_documents_paths' => !empty($supportingDocumentsPaths) ? $supportingDocumentsPaths : null,
                'authorized_signatory_name' => $request->authorized_signatory_name ?? 'Not Provided',
                'authorized_signatory_email' => $request->authorized_signatory_email ?? 'Not Provided',
                'authorized_signatory_phone' => $request->authorized_signatory_phone ?? 'Not Provided',
                'digital_signatures_paths' => !empty($digitalSignaturesPaths) ? $digitalSignaturesPaths : null,
                'credit_application_status' => 'pending',
            ];

            Log::info('Creating credit application', [
                'contact_id' => $contact->id,
                'application_data' => $applicationData,
                'supporting_docs_count' => count($supportingDocumentsPaths),
                'digital_signatures_count' => count($digitalSignaturesPaths)
            ]);

            try {
                $creditApplication = CreditApplication::create($applicationData);
                Log::info('Credit application created successfully', [
                    'contact_id' => $contact->id,
                    'application_id' => $creditApplication->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create credit application', [
                    'contact_id' => $contact->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            return response()->json([
                'status' => true,
                'message' => 'Credit application submitted successfully',
                'data' => [
                    'application_id' => $creditApplication->id,
                    'customer_name' => $contact->name,
                    'business_name' => $contact->supplier_business_name,
                    'submitted_at' => now(),
                    'credit_application' => [
                        'requested_credit_amount' => $creditApplication->requested_credit_amount,
                        'average_revenue_per_month' => $creditApplication->average_revenue_per_month,
                        'supporting_documents_paths' => $creditApplication->supporting_documents_paths,
                        'authorized_signatory_name' => $creditApplication->authorized_signatory_name,
                        'authorized_signatory_email' => $creditApplication->authorized_signatory_email,
                        'authorized_signatory_phone' => $creditApplication->authorized_signatory_phone,
                        'digital_signatures_paths' => $creditApplication->digital_signatures_paths,
                    ],
                    'upload_summary' => [
                        'supporting_documents_count' => is_array($creditApplication->supporting_documents_paths) ? count($creditApplication->supporting_documents_paths) : 0,
                        'digital_signatures_count' => is_array($creditApplication->digital_signatures_paths) ? count($creditApplication->digital_signatures_paths) : 0,
                    ]
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Throwable $th) {
            Log::error('Failed to store credit application: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
                'request_data' => $request->all(),
                'contact_id' => $contact->id ?? null
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to submit credit application',
                'error' => $th->getMessage(), // Show actual error for调试
                'debug_info' => [
                    'file' => $th->getFile(),
                    'line' => $th->getLine(),
                ]
            ], 500);
        }
    }

    /**
     * Store comprehensive credit application with all form fields
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeComprehensiveCreditApplication(Request $request)
    {
        try {
            // Check authentication
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication required to submit credit application'
                ], 401);
            }

            $contact = $authData['user'];
            
            // Handle JSON input if Content-Type is application/json
            if ($request->isJson() || $request->header('Content-Type') === 'application/json') {
                // Merge JSON body into request
                $jsonData = $request->json()->all();
                if (!empty($jsonData)) {
                    // Flatten nested JSON structure for validation
                    if (isset($jsonData['company_information'])) {
                        $request->merge($jsonData['company_information']);
                    }
                    if (isset($jsonData['owners'])) {
                        $request->merge(['owners' => $jsonData['owners']]);
                    }
                    // Merge top-level fields
                    $request->merge(array_diff_key($jsonData, ['company_information' => '', 'owners' => '']));
                }
            }

            // Check for pending application
            $pendingApplication = CreditApplication::where('contact_id', $contact->id)
                ->where('credit_application_status', 'pending')
                ->first();

            if ($pendingApplication) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already have a pending credit application under review. Please wait for approval or rejection before submitting a new request.'
                ], 400);
            }

            // Validate required fields
            $validationRules = [
                
                // Company Information
                'requested_credit_amount' => 'required|numeric|min:0',
                'average_revenue_per_month' => 'required|numeric|min:0',
                'legal_business_name' => 'required|string|max:255',
                'legal_address' => 'required|string|max:500',
                'legal_city_state_zip' => 'required|string|max:255',
                'dba_name' => 'nullable|string|max:255',
                'dba_address' => 'required|string|max:500',
                'dba_city_state_zip' => 'required|string|max:255',
                'website' => 'nullable|url|max:255',
                'business_email' => 'required|email|max:255',
                'business_contact_name' => 'required|string|max:255',
                'business_phone' => 'required|string|max:20',
                'cust_svc_phone' => 'nullable|string|max:20',
                'fed_tax_id' => 'required|string|max:20',
                'business_start_date' => 'required|date',
                'products_sold' => 'nullable|array',
                'products_sold.*' => 'string|in:CBD/Delta,Kava,THCA,Mushrooms,Kratom,Hemp Seeds',
                'type_of_corporation' => 'required|string|in:Sole Proprietorship,LLC/LLP,Corporation,Non-Profit',
                'currently_accept_cards' => 'required|string|in:Y,N',
                
                // Ownership Information (array of owners)
                'owners' => 'required|array|min:1',
                'owners.*.name' => 'required|string|max:255',
                'owners.*.email' => 'required|email|max:255',
                'owners.*.date_of_birth' => 'required|date',
                'owners.*.ssn' => 'required|string|max:20',
                'owners.*.title' => 'required|string|max:255',
                'owners.*.address' => 'required|string|max:500',
                'owners.*.city_state_zip' => 'required|string|max:255',
                'owners.*.phone' => 'required|string|max:20',
                'owners.*.ownership_percentage' => 'required|numeric|min:0|max:100',
                'owners.*.dl_number' => 'nullable|string|max:50',
                'owners.*.dl_state' => 'nullable|string|max:50',
            ];

            // Only validate file uploads if files are actually being uploaded (multipart/form-data)
            // Skip file validation if request is JSON (files can't be sent in JSON)
            $isJsonRequest = $request->isJson() || 
                           $request->header('Content-Type') === 'application/json' ||
                           str_contains($request->header('Content-Type', ''), 'application/json');
            
            $hasFiles = false;
            if (!$isJsonRequest) {
                // Check if any owner has file uploads (only for multipart/form-data)
                if (is_array($request->input('owners', []))) {
                    foreach ($request->input('owners', []) as $index => $owner) {
                        if ($request->hasFile("owners.$index.bank_statement") || 
                            $request->hasFile("owners.$index.driver_license")) {
                            $hasFiles = true;
                            break;
                        }
                        // Check for array of files
                        if ($request->hasFile("owners.$index.supporting_documents")) {
                            $hasFiles = true;
                            break;
                        }
                        if ($request->hasFile("owners.$index.digital_signatures")) {
                            $hasFiles = true;
                            break;
                        }
                    }
                }
            }

            // Add file validation rules only if files are being uploaded
            if ($hasFiles) {
                $validationRules['owners.*.bank_statement'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240';
                $validationRules['owners.*.driver_license'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240';
                // For supporting_documents and digital_signatures, allow either a single file
                // or an array of files. We keep the per-file rules on the wildcard.
                $validationRules['owners.*.supporting_documents'] = 'nullable';
                $validationRules['owners.*.supporting_documents.*'] = 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240';
                $validationRules['owners.*.digital_signatures'] = 'nullable';
                $validationRules['owners.*.digital_signatures.*'] = 'file|mimes:pdf,jpg,jpeg,png|max:10240';
            }

            $request->validate($validationRules);

            DB::beginTransaction();

            $destination_path = public_path('uploads/img/credit_applications');
            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0755, true);
            }

            // Process owners and their documents
            $ownersData = [];
            foreach ($request->owners as $index => $ownerData) {
                $ownerInfo = [
                    'name' => $ownerData['name'],
                    'email' => $ownerData['email'],
                    'date_of_birth' => $ownerData['date_of_birth'],
                    'ssn' => $ownerData['ssn'],
                    'title' => $ownerData['title'],
                    'address' => $ownerData['address'],
                    'city_state_zip' => $ownerData['city_state_zip'],
                    'phone' => $ownerData['phone'],
                    'ownership_percentage' => $ownerData['ownership_percentage'],
                    'dl_number' => $ownerData['dl_number'] ?? null,
                    'dl_state' => $ownerData['dl_state'] ?? null,
                    'documents' => []
                ];

                // Upload bank statement
                if (isset($ownerData['bank_statement']) && $request->hasFile("owners.$index.bank_statement")) {
                    $file = $request->file("owners.$index.bank_statement");
                    $secureFilename = uniqid() . '_bank_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $file->move($destination_path, $secureFilename);
                    $ownerInfo['documents']['bank_statement'] = [
                        'file_path' => 'uploads/img/credit_applications/' . $secureFilename,
                        'original_name' => $file->getClientOriginalName(),
                        'uploaded_at' => now()->toDateTimeString()
                    ];
                }

                // Upload driver license
                if (isset($ownerData['driver_license']) && $request->hasFile("owners.$index.driver_license")) {
                    $file = $request->file("owners.$index.driver_license");
                    $secureFilename = uniqid() . '_dl_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $file->move($destination_path, $secureFilename);
                    $ownerInfo['documents']['driver_license'] = [
                        'file_path' => 'uploads/img/credit_applications/' . $secureFilename,
                        'original_name' => $file->getClientOriginalName(),
                        'uploaded_at' => now()->toDateTimeString()
                    ];
                }

                // Upload supporting documents (supports single file or array)
                if ($request->hasFile("owners.$index.supporting_documents")) {
                    $supportingDocs = [];
                    $docs = $request->file("owners.$index.supporting_documents");
                    $docs = is_array($docs) ? $docs : [$docs];
                    foreach ($docs as $doc) {
                        $secureFilename = uniqid() . '_support_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $doc->getClientOriginalName());
                        $doc->move($destination_path, $secureFilename);
                        $supportingDocs[] = [
                            'file_path' => 'uploads/img/credit_applications/' . $secureFilename,
                            'original_name' => $doc->getClientOriginalName(),
                            'uploaded_at' => now()->toDateTimeString()
                        ];
                    }
                    $ownerInfo['documents']['supporting_documents'] = $supportingDocs;
                }

                // Upload digital signatures (supports single file or array)
                if ($request->hasFile("owners.$index.digital_signatures")) {
                    $signatures = [];
                    $sigs = $request->file("owners.$index.digital_signatures");
                    $sigs = is_array($sigs) ? $sigs : [$sigs];
                    foreach ($sigs as $sig) {
                        $secureFilename = uniqid() . '_signature_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $sig->getClientOriginalName());
                        $sig->move($destination_path, $secureFilename);
                        $signatures[] = [
                            'file_path' => 'uploads/img/credit_applications/' . $secureFilename,
                            'original_name' => $sig->getClientOriginalName(),
                            'uploaded_at' => now()->toDateTimeString()
                        ];
                    }
                    $ownerInfo['documents']['digital_signatures'] = $signatures;
                }

                $ownersData[] = $ownerInfo;
            }

            // Extract first owner for separate fields, rest go to additional_owners
            $firstOwner = !empty($ownersData) ? $ownersData[0] : null;
            $additionalOwners = count($ownersData) > 1 ? array_slice($ownersData, 1) : null;

            // Prepare comprehensive application data
            // Separate application data (form fields) from document paths
            $applicationDataToSave = [
                'contact_id' => $contact->id,
                'requested_credit_amount' => $request->requested_credit_amount,
                'average_revenue_per_month' => $request->average_revenue_per_month,
                'credit_application_status' => 'pending',
                // Store all form data in application_data field (for backward compatibility)
                'application_data' => [
                    'company_information' => [
                        'legal_business_name' => $request->legal_business_name,
                        'legal_address' => $request->legal_address,
                        'legal_city_state_zip' => $request->legal_city_state_zip,
                        'dba_name' => $request->dba_name,
                        'dba_address' => $request->dba_address,
                        'dba_city_state_zip' => $request->dba_city_state_zip,
                        'website' => $request->website,
                        'business_email' => $request->business_email,
                        'business_contact_name' => $request->business_contact_name,
                        'business_phone' => $request->business_phone,
                        'cust_svc_phone' => $request->cust_svc_phone,
                        'fed_tax_id' => $request->fed_tax_id,
                        'business_start_date' => $request->business_start_date,
                        'products_sold' => $request->products_sold ?? [],
                        'type_of_corporation' => $request->type_of_corporation,
                        'currently_accept_cards' => $request->currently_accept_cards,
                        'requested_credit_amount' => $request->requested_credit_amount,
                        'average_revenue_per_month' => $request->average_revenue_per_month
                    ],
                    'owners' => $ownersData
                ],
                // Store only document file paths in supporting_documents_paths (for backward compatibility)
                'supporting_documents_paths' => $this->extractDocumentPathsFromOwners($ownersData),
                'authorized_signatory_name' => $request->business_contact_name,
                'authorized_signatory_email' => $request->business_email,
                'authorized_signatory_phone' => $request->business_phone,
                'digital_signatures_paths' => [],
                // Store primary owner in separate fields
                'owner_name' => $firstOwner['name'] ?? null,
                'owner_email' => $firstOwner['email'] ?? null,
                'owner_date_of_birth' => isset($firstOwner['date_of_birth']) ? $firstOwner['date_of_birth'] : null,
                'owner_ssn' => $firstOwner['ssn'] ?? null,
                'owner_title' => $firstOwner['title'] ?? null,
                'owner_address' => $firstOwner['address'] ?? null,
                'owner_city_state_zip' => $firstOwner['city_state_zip'] ?? null,
                'owner_phone' => $firstOwner['phone'] ?? null,
                'owner_ownership_percentage' => $firstOwner['ownership_percentage'] ?? null,
                'owner_dl_number' => $firstOwner['dl_number'] ?? null,
                'owner_dl_state' => $firstOwner['dl_state'] ?? null,
                // Store additional owners (if any) in JSON field
                'additional_owners' => $additionalOwners,
            ];

            // Collect all digital signatures from owners
            $allDigitalSignatures = [];
            foreach ($ownersData as $owner) {
                if (isset($owner['documents']['digital_signatures']) && is_array($owner['documents']['digital_signatures'])) {
                    $allDigitalSignatures = array_merge(
                        $allDigitalSignatures,
                        $owner['documents']['digital_signatures']
                    );
                }
            }
            $applicationDataToSave['digital_signatures_paths'] = $allDigitalSignatures;

            // Log the data structure before saving for debugging
            Log::info('Credit Application Data Structure', [
                'contact_id' => $applicationDataToSave['contact_id'],
                'requested_credit_amount' => $applicationDataToSave['requested_credit_amount'],
                'average_revenue_per_month' => $applicationDataToSave['average_revenue_per_month'],
                'company_info_fields' => count($applicationDataToSave['application_data']['company_information'] ?? []),
                'owners_count' => count($applicationDataToSave['application_data']['owners'] ?? []),
                'digital_signatures_count' => count($applicationDataToSave['digital_signatures_paths'] ?? []),
                'has_company_info' => !empty($applicationDataToSave['application_data']['company_information'] ?? []),
                'has_owners' => !empty($applicationDataToSave['application_data']['owners'] ?? []),
            ]);

            $creditApplication = CreditApplication::create($applicationDataToSave);

            // Verify data was saved correctly
            $savedApplication = CreditApplication::find($creditApplication->id);
            Log::info('Credit Application Saved Verification', [
                'application_id' => $savedApplication->id,
                'requested_credit_amount' => $savedApplication->requested_credit_amount,
                'average_revenue_per_month' => $savedApplication->average_revenue_per_month,
                'application_data_is_array' => is_array($savedApplication->application_data),
                'supporting_docs_is_array' => is_array($savedApplication->supporting_documents_paths),
                'digital_sigs_is_array' => is_array($savedApplication->digital_signatures_paths),
                'has_company_info' => isset($savedApplication->application_data['company_information']),
                'has_owners' => isset($savedApplication->application_data['owners']),
                'owners_count' => isset($savedApplication->application_data['owners']) 
                    ? count($savedApplication->application_data['owners']) 
                    : 0,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Credit application submitted successfully',
                'data' => [
                    'application_id' => $creditApplication->id,
                    'customer_name' => $contact->name,
                    'business_name' => $request->legal_business_name,
                    'submitted_at' => now()->toDateTimeString(),
                    'status' => 'pending',
                    'summary' => [
                        'owners_count' => count($ownersData),
                        'total_ownership_percentage' => array_sum(array_column($ownersData, 'ownership_percentage')),
                    ]
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Failed to store comprehensive credit application: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to submit credit application',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Extract document file paths from owners data
     * Returns only file paths for backward compatibility with supporting_documents_paths
     * 
     * @param array $ownersData
     * @return array
     */
    private function extractDocumentPathsFromOwners($ownersData)
    {
        $documentPaths = [];
        
        foreach ($ownersData as $owner) {
            if (isset($owner['documents'])) {
                $ownerDocs = [];
                
                // Bank statement
                if (isset($owner['documents']['bank_statement']['file_path'])) {
                    $ownerDocs['bank_statement'] = $owner['documents']['bank_statement']['file_path'];
                }
                
                // Driver license
                if (isset($owner['documents']['driver_license']['file_path'])) {
                    $ownerDocs['driver_license'] = $owner['documents']['driver_license']['file_path'];
                }
                
                // Supporting documents (array of paths)
                if (isset($owner['documents']['supporting_documents']) && is_array($owner['documents']['supporting_documents'])) {
                    $ownerDocs['supporting_documents'] = array_column($owner['documents']['supporting_documents'], 'file_path');
                }
                
                if (!empty($ownerDocs)) {
                    $documentPaths[] = $ownerDocs;
                }
            }
        }
        
        return $documentPaths;
    }

    private function authCheck($request)
    {
        $contact = Auth::guard('api')->user();
        if ($contact) {
            return [
                'status' => true,
                'user' => $contact
            ];
        } else {
            return [
                'status' => false,
                'message' => 'User not authenticated',
            ];
        }
    }
    /**
     * Get all credit applications for the authenticated customer (API)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getcustomerCreditApplications(Request $request)
    {
        try {
            // Check authentication
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication required to view credit applications'
                ], 401);
            }

            $contact = $authData['user'];

            // Get all credit applications for this customer, ordered by most recent first
            $creditApplications = CreditApplication::where('contact_id', $contact->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get current credit limit
            $currentCreditLimit = $contact->credit_limit ?? 0;
            
            // Check if customer has a pending application
            $hasPendingApplication = $creditApplications->where('credit_application_status', 'pending')->isNotEmpty();
            
            // Get the most recent approved application
            $lastApprovedApplication = $creditApplications->where('credit_application_status', 'approved')->first();

            // Format the response data
            $formattedApplications = $creditApplications->map(function($application) {
                return [
                    'id' => $application->id,
                    'requested_credit_amount' => $application->requested_credit_amount,
                    'average_revenue_per_month' => $application->average_revenue_per_month,
                    'credit_application_status' => $application->credit_application_status,
                    'status_label' => ucfirst($application->credit_application_status ?? 'pending'),
                    'authorized_signatory_name' => $application->authorized_signatory_name,
                    'authorized_signatory_email' => $application->authorized_signatory_email,
                    'authorized_signatory_phone' => $application->authorized_signatory_phone,
                    'supporting_documents_count' => is_array($application->supporting_documents_paths) ? count($application->supporting_documents_paths) : 0,
                    'digital_signatures_count' => is_array($application->digital_signatures_paths) ? count($application->digital_signatures_paths) : 0,
                    'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $application->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Credit applications retrieved successfully',
                'data' => [
                    'customer' => [
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'business_name' => $contact->supplier_business_name,
                        'email' => $contact->email,
                        'current_credit_limit' => $currentCreditLimit,
                    ],
                    'summary' => [
                        'total_applications' => $creditApplications->count(),
                        'pending_applications' => $creditApplications->where('credit_application_status', 'pending')->count(),
                        'approved_applications' => $creditApplications->where('credit_application_status', 'approved')->count(),
                        'rejected_applications' => $creditApplications->where('credit_application_status', 'rejected')->count(),
                        'has_pending_application' => $hasPendingApplication,
                        'can_apply_new' => !$hasPendingApplication, // Can apply only if no pending application
                        'last_approved_limit' => $lastApprovedApplication ? $currentCreditLimit : 0,
                    ],
                    'applications' => $formattedApplications,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to fetch customer credit applications: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve credit applications',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }
}
