<?php

namespace App\Http\Controllers;

use App\Models\MerchantApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\MerchantApplicationSubmitted;
use App\Mail\MerchantApplicationResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MerchantApplicationController extends Controller
{
    public function index()
    {

        if (request()->ajax()) {
        $applications = MerchantApplication::query();
        return DataTables::of($applications)
            ->addColumn('actions', function($application) {
                $viewBtn = '<a href="'.route('merchant-applications.show', $application->id).'" class="btn btn-sm btn-info">View</a>';
                $approveBtn = '';
                $rejectBtn = '';
                if ($application->status === 'pending') {
                    $approveBtn = '<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal'.$application->id.'">Approve</button>';
                    $rejectBtn = '<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal'.$application->id.'">Reject</button>';
                }
                return $viewBtn . ' ' . $approveBtn . ' ' . $rejectBtn;
            })
            ->editColumn('status', function($application) {
                $badgeClass = $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning');
                return '<span class="badge badge-'.$badgeClass.'">'.ucfirst($application->status).'</span>';
            })
            ->editColumn('created_at', function($application) {
                return $application->created_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    return view('merchant_applications.index');
        // $applications = MerchantApplication::orderBy('created_at', 'desc')->paginate(10);
        // return view('merchant_applications.index', compact('applications'));
    }

    public function create()
    {
        return view('merchant_applications.create');
    }

    // handle merchant application api
    public function merchantApplicationApi(Request $request)
    {
        Log::info($request->all());
        return response()->json(['message' => 'Merchant application api called'],200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Business Info
            'legal_business_name' => 'required|string|max:255',
            'legal_address' => 'required|string|max:255',
            'legal_city' => 'required|string|max:255',
            'legal_state' => 'required|string|max:2',
            'legal_zip' => 'required|string|max:10',
            'dba_name' => 'nullable|string|max:255',
            'dba_address' => 'nullable|string|max:255',
            'dba_city' => 'nullable|string|max:255',
            'dba_state' => 'nullable|string|max:2',
            'dba_zip' => 'nullable|string|max:10',
            'business_type' => 'required|string|max:50',
            'federal_tax_id' => 'required|string|max:50',
            'business_age' => 'required|string|max:50',
            'business_phone' => 'required|string|min:10|max:10',
            'website' => 'nullable|url|max:255',
            
            // Ownership Info
            'owner_legal_name' => 'required|string|max:255',
            'ownership_percentage' => 'required|numeric|min:0|max:100',
            'job_title' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'owner_address' => 'required|string|max:255',
            'owner_city' => 'required|string|max:255',
            'owner_state' => 'required|string|max:2',
            'owner_zip' => 'required|string|max:10',
            'owner_email' => 'required|email|max:255',
            'owner_phone' => 'required|string|min:10|max:10',
            'owner_ssn' => 'nullable|string|max:11',
            
            // Previous Processing
            'has_previous_processing' => 'required|boolean',
            'processing_duration' => 'required_if:has_previous_processing,1|nullable|string|max:50',
            'previous_processor' => 'required_if:has_previous_processing,1|nullable|string|max:255',
            'average_ticket_amount' => 'required_if:has_previous_processing,1|nullable|numeric|min:0',
            'monthly_volume' => 'required_if:has_previous_processing,1|nullable|numeric|min:0',
            
            // Documents
            'voided_check' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'driver_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'processing_statements' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            
            // Additional Owners
            'additional_owners' => 'nullable|array',
            'additional_owners.*.name' => 'required|string|max:255',
            'additional_owners.*.percentage' => 'required|numeric|min:0|max:100',
            'additional_owners.*.dob' => 'required|date',
            'additional_owners.*.ssn' => 'required|string|max:11',

            // Gateway option
            'gateway_option' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Handle file uploads
            $voidedCheckPath = $request->file('voided_check')->store('merchant_documents');
            $driverLicensePath = $request->file('driver_license')->store('merchant_documents');
            $processingStatementsPath = $request->hasFile('processing_statements') 
                ? $request->file('processing_statements')->store('merchant_documents')
                : null;

            // Create application
            $application = MerchantApplication::create([
                'legal_business_name' => $request->legal_business_name,
                'legal_address' => $request->legal_address,
                'legal_city' => $request->legal_city,
                'legal_state' => $request->legal_state,
                'legal_zip' => $request->legal_zip,
                'dba_name' => $request->dba_name,
                'dba_address' => $request->dba_address,
                'dba_city' => $request->dba_city,
                'dba_state' => $request->dba_state,
                'dba_zip' => $request->dba_zip,
                'business_type' => $request->business_type,
                'federal_tax_id' => $request->federal_tax_id,
                'business_age' => $request->business_age,
                'business_phone' => $request->business_phone,
                'website' => $request->website,
                'owner_legal_name' => $request->owner_legal_name,
                'ownership_percentage' => $request->ownership_percentage,
                'job_title' => $request->job_title,
                'date_of_birth' => $request->date_of_birth,
                'owner_address' => $request->owner_address,
                'owner_city' => $request->owner_city,
                'owner_state' => $request->owner_state,
                'owner_zip' => $request->owner_zip,
                'owner_email' => $request->owner_email,
                'owner_phone' => $request->owner_phone,
                'owner_ssn' => $request->owner_ssn??'NA',
                'has_previous_processing' => $request->has_previous_processing,
                'processing_duration' => $request->processing_duration,
                'previous_processor' => $request->previous_processor,
                'average_ticket_amount' => $request->average_ticket_amount,
                'monthly_volume' => $request->monthly_volume,
                'voided_check_path' => $voidedCheckPath,
                'driver_license_path' => $driverLicensePath,
                'processing_statements_path' => $processingStatementsPath,
                'additional_owners' => $request->additional_owners,
                'created_by' => auth()->user()->id,
                'gateway_option' => $request->gateway_option,
            ]);

            // Send email notification
            $email = '';
            switch($request->gateway_option){
                case 'nmi':
                    $email = config('mail.nmi_team');
                    break;
                case 'authorize':
                    // mail to authorize team
                    $email = config('mail.authorize_team');
                    break;
                case 'square':
                    // mail to square team
                    $email = config('mail.square_team');
                    break;
                case 'razorpay':
                    // mail to razorpay team
                    $email = config('mail.razorpay_team');
                    break;
                case 'stripe':
                    // mail to stripe team
                    $email = config('mail.stripe_team');
                    break;
                case 'paypal':
                    // mail to paypal team
                    $email = config('mail.paypal_team');
                    break;
                case 'other':
                    // mail to Phantasm ERP team
                    $email = config('mail.other_team');
                    break;
            }
            // Mail::to($email)->send(new MerchantApplicationSubmitted($application));

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully and email sent to ' . $email,
                'data' => $application
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(MerchantApplication $application)
    {
        return view('merchant_applications.show', compact('application'));
    }

    public function update(Request $request, MerchantApplication $application)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'required|string',
            'admin_response' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $application->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
                'admin_response' => $request->admin_response,
                'updated_by' => auth()->id(),
            ]);

            // Send email response to applicant
            Mail::to($application->owner_email)->send(new MerchantApplicationResponse($application));

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully',
                'data' => $application
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(MerchantApplication $application)
    {
        try {
            // Delete associated files
            Storage::delete([
                $application->voided_check_path,
                $application->driver_license_path,
                $application->processing_statements_path
            ]);

            $application->delete();

            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting application: ' . $e->getMessage()
            ], 500);
        }
    }
} 