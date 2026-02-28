<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Events\TicketMessageEvent;
use App\Lead;
use App\Ticket;
use App\TicketActivity;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TicketController extends Controller
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
     * Check if user has access to B2B locations
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
     * Check if user has access to B2C locations
     *
     * @return bool
     */
    private function hasB2CAccess()
    {
        $user = auth()->user();
        $business_id = session('business.id');
        
        if (!$business_id) {
            return false;
        }
        
        $permitted_locations = $user->permitted_locations($business_id);
        
        if ($permitted_locations == 'all') {
            // User has access to all locations, check if any location is B2C
            return BusinessLocation::where('business_id', $business_id)
                ->where('is_b2c', 1)
                ->exists();
        } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
            // User has specific location permissions, check if any is B2C
            return BusinessLocation::whereIn('id', $permitted_locations)
                ->where('is_b2c', 1)
                ->exists();
        }
        
        return false;
    }

    /**
     * Check if user is authorized to access tickets
     * Tickets are accessible to users with user.view permission AND (B2B OR B2C) location access
     * Or Admin role
     *
     * @return bool
     */
    private function isAuthorized()
    {
        $user = auth()->user();
        $business_id = session('business.id');
        
        // Admin always has access
        if ($user->hasRole('Admin#' . $business_id)) {
            return true;
        }
        
        // Check if user has user.view permission (matches sidebar menu check)
        if (!$user->can('user.view')) {
            return false;
        }
        
        // Check for B2B or B2C access
        return $this->hasB2BAccess() || $this->hasB2CAccess();
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

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $tickets = Ticket::whereHas('lead', function ($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })
            ->with(['lead', 'user'])
            ->select('tickets.*');

            return Datatables::of($tickets)
                ->addColumn('reference_no', function ($row) {
                    return $row->reference_no ?? 'N/A';
                })
                ->addColumn('lead_name', function ($row) {
                    return $row->lead ? $row->lead->store_name : 'N/A';
                })
                ->addColumn('assigned_to', function ($row) {
                    return $row->user ? $row->user->first_name . ' ' . $row->user->last_name : 'N/A';
                })
                ->addColumn('issue_type_badge', function ($row) {
                    if (!$row->issue_type) {
                        return '<span class="label label-default">N/A</span>';
                    }
                    $issueTypeColors = [
                        'technical' => 'danger',
                        'billing' => 'warning',
                        'product' => 'info',
                        'service' => 'primary',
                        'complaint' => 'danger',
                        'inquiry' => 'success',
                        'other' => 'default'
                    ];
                    $color = $issueTypeColors[$row->issue_type] ?? 'default';
                    return '<span class="label label-' . $color . '">' . ucfirst(str_replace('_', ' ', $row->issue_type)) . '</span>';
                })
                ->addColumn('issue_priority_badge', function ($row) {
                    if (!$row->issue_priority) {
                        return '<span class="label label-default">Medium</span>';
                    }
                    $priorityColors = [
                        'low' => 'success',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger'
                    ];
                    $color = $priorityColors[$row->issue_priority] ?? 'info';
                    return '<span class="label label-' . $color . '">' . ucfirst($row->issue_priority) . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $statusColors = [
                        'open' => 'success',
                        'in_progress' => 'info',
                        'closed' => 'default',
                        'pending' => 'warning',
                        'resolved' => 'primary'
                    ];
                    $color = $statusColors[$row->status] ?? 'default';
                    return '<span class="label label-' . $color . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div>';
                    
                    $html .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info btn-modal" data-href="' . action([\App\Http\Controllers\TicketController::class, 'show'], [$row->id]) . '" data-container=".ticket_modal" title="View">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                              </button>';
                    
                    $html .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-warning btn-modal" data-href="' . action([\App\Http\Controllers\TicketController::class, 'edit'], [$row->id]) . '" data-container=".ticket_modal" title="Edit">
                                <i class="fa fa-edit" aria-hidden="true"></i>
                              </button>';
                    
                    $html .= '<a href="' . action([\App\Http\Controllers\TicketController::class, 'destroy'], [$row->id]) . '" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_ticket" title="Delete">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                              </a>';

                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['action', 'status_badge', 'issue_type_badge', 'issue_priority_badge'])
                ->make(true);
        }

        return view('ticket.index');
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

        $business_id = request()->session()->get('user.business_id');
        $leads = Lead::leadsDropdown($business_id, false);
        $users = User::saleCommissionAgentsDropdown($business_id, false);
        
        // Check which columns exist
        $hasIssueType = \Schema::hasColumn('tickets', 'issue_type');
        $hasIssuePriority = \Schema::hasColumn('tickets', 'issue_priority');
        $hasInitialImage = \Schema::hasColumn('tickets', 'initial_image');

        return view('ticket.create')
            ->with(compact('leads', 'users', 'hasIssueType', 'hasIssuePriority', 'hasInitialImage'));
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
            // Check which columns exist for backward compatibility
            $hasIssueType = \Schema::hasColumn('tickets', 'issue_type');
            $hasIssuePriority = \Schema::hasColumn('tickets', 'issue_priority');
            $hasInitialImage = \Schema::hasColumn('tickets', 'initial_image');
            
            // Build validation rules dynamically
            $rules = [
                'lead_id' => 'required|exists:leads,id',
                'user_id' => 'required|exists:users,id',
                'ticket_description' => 'required|string',
                'status' => 'required|string|in:open,in_progress,pending,resolved',
            ];
            
            if ($hasIssueType) {
                $rules['issue_type'] = 'required|string|in:technical,billing,product,service,complaint,inquiry,other';
            }
            if ($hasIssuePriority) {
                $rules['issue_priority'] = 'required|string|in:low,medium,high,urgent';
            }
            if ($hasInitialImage) {
                $rules['initial_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120';
            }
            
            $request->validate($rules);

            // Get only the fields that exist
            $inputFields = ['lead_id', 'user_id', 'ticket_description', 'status'];
            if ($hasIssueType) {
                $inputFields[] = 'issue_type';
            }
            if ($hasIssuePriority) {
                $inputFields[] = 'issue_priority';
            }
            
            $input = $request->only($inputFields);
            
            // Handle initial image upload (only if column exists)
            if ($hasInitialImage && $request->hasFile('initial_image')) {
                $image = $request->file('initial_image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $uploadPath = public_path('uploads/tickets');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $image->move($uploadPath, $imageName);
                $input['initial_image'] = $imageName;
            }
            
            // Generate reference number with TI prefix + 6 digits
            $lastTicket = Ticket::whereNotNull('reference_no')
                ->orderBy('id', 'desc')
                ->first();
            
            if ($lastTicket && $lastTicket->reference_no) {
                // Extract number from last reference (e.g., TI000001 -> 1)
                $lastNumber = intval(substr($lastTicket->reference_no, 2));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            // Format as TI + 6 digits (e.g., TI000001)
            $input['reference_no'] = 'TI' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            
            $ticket = Ticket::create($input);

            // Create initial activity
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->user()->id,
                'activity_type' => 'created',
                'activity_details' => 'Ticket created'
            ]);

            $output = [
                'success' => true,
                'data' => $ticket,
                'msg' => 'Ticket created successfully'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            $output = [
                'success' => false,
                'msg' => 'Validation error: ' . implode(', ', $e->validator->errors()->all())
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => 'Something went wrong: ' . $e->getMessage()
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
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $ticket = Ticket::whereHas('lead', function ($query) use ($business_id) {
            $query->where('business_id', $business_id);
        })
        ->with(['lead', 'user', 'activities.user'])
        ->findOrFail($id);

        return view('ticket.show_modal')
            ->with(compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $ticket = Ticket::whereHas('lead', function ($query) use ($business_id) {
            $query->where('business_id', $business_id);
        })->findOrFail($id);

        $leads = Lead::leadsDropdown($business_id, false);
        $users = User::saleCommissionAgentsDropdown($business_id, false);
        
        // Check which columns exist
        $hasIssueType = \Schema::hasColumn('tickets', 'issue_type');
        $hasIssuePriority = \Schema::hasColumn('tickets', 'issue_priority');
        $hasInitialImage = \Schema::hasColumn('tickets', 'initial_image');

        return view('ticket.edit_modal')
            ->with(compact('ticket', 'leads', 'users', 'hasIssueType', 'hasIssuePriority', 'hasInitialImage'));
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
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Check which columns exist for backward compatibility
            $hasIssueType = \Schema::hasColumn('tickets', 'issue_type');
            $hasIssuePriority = \Schema::hasColumn('tickets', 'issue_priority');
            $hasInitialImage = \Schema::hasColumn('tickets', 'initial_image');
            
            // Build validation rules dynamically
            $rules = [
                'lead_id' => 'required|exists:leads,id',
                'user_id' => 'required|exists:users,id',
                'ticket_description' => 'required|string',
                'status' => 'required|string|in:open,in_progress,pending,resolved,closed',
            ];
            
            if ($hasIssueType) {
                $rules['issue_type'] = 'required|string|in:technical,billing,product,service,complaint,inquiry,other';
            }
            if ($hasIssuePriority) {
                $rules['issue_priority'] = 'required|string|in:low,medium,high,urgent';
            }
            if ($hasInitialImage) {
                $rules['initial_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120';
            }
            
            $request->validate($rules);

            $business_id = request()->session()->get('user.business_id');
            $ticket = Ticket::whereHas('lead', function ($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })->findOrFail($id);

            // Get only the fields that exist
            $inputFields = ['lead_id', 'user_id', 'ticket_description', 'status'];
            if ($hasIssueType) {
                $inputFields[] = 'issue_type';
            }
            if ($hasIssuePriority) {
                $inputFields[] = 'issue_priority';
            }
            
            $input = $request->only($inputFields);
            
            // Handle initial image upload (only if column exists)
            if ($hasInitialImage && $request->hasFile('initial_image')) {
                $uploadPath = public_path('uploads/tickets');
                
                // Delete old image if exists
                if ($ticket->initial_image && file_exists($uploadPath . '/' . $ticket->initial_image)) {
                    unlink($uploadPath . '/' . $ticket->initial_image);
                }
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $image = $request->file('initial_image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($uploadPath, $imageName);
                $input['initial_image'] = $imageName;
            }
            
            $ticket->update($input);

            // Create activity log
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->user()->id,
                'activity_type' => 'updated',
                'activity_details' => 'Ticket updated'
            ]);

            $output = [
                'success' => true,
                'data' => $ticket,
                'msg' => 'Ticket updated successfully'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            $output = [
                'success' => false,
                'msg' => 'Validation error: ' . implode(', ', $e->validator->errors()->all())
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => 'Something went wrong: ' . $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Add a message to the ticket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addMessage(Request $request, $id)
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $ticket = Ticket::whereHas('lead', function ($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })->findOrFail($id);

            $message = $request->input('message');
            $attachment = null;
            $activityType = 'text'; // Default type

            // Handle file upload
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Create directory if it doesn't exist
                $uploadPath = public_path('uploads/tickets');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $file->move($uploadPath, $fileName);
                $attachment = $fileName;

                // Determine activity type based on file extension
                $extension = strtolower($file->getClientOriginalExtension());
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $activityType = 'image';
                } else {
                    $activityType = 'file';
                }

                // If no message provided for file upload, use filename
                if (empty($message)) {
                    $message = 'Sent a file: ' . $file->getClientOriginalName();
                }
            }

            // Validate: either message or attachment must be present
            if (empty($message) && empty($attachment)) {
                $output = [
                    'success' => false,
                    'msg' => 'Please enter a message or upload a file'
                ];
                return $output;
            }

            // Create activity
            $activity = TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->user()->id,
                'activity_type' => $activityType,
                'activity_details' => $message,
                'attachment' => $attachment
            ]);

            // Load user relationship
            $activity->load('user');

            // Broadcast the message via WebSocket
            broadcast(new TicketMessageEvent($activity))->toOthers();

            // Prepare activity data for response
            $activityData = [
                'id' => $activity->id,
                'ticket_id' => $activity->ticket_id,
                'user_id' => $activity->user_id,
                'activity_type' => $activity->activity_type,
                'activity_details' => $activity->activity_details,
                'attachment' => $activity->attachment,
                'file_url' => $activity->file_url,
                'created_at' => $activity->created_at->toISOString(),
                'user' => [
                    'id' => $activity->user->id,
                    'name' => $activity->user->first_name . ' ' . $activity->user->last_name,
                    'first_name' => $activity->user->first_name,
                    'last_name' => $activity->user->last_name,
                ],
                'is_image' => $activity->isImage(),
                'file_extension' => $activity->getFileExtension(),
            ];

            $output = [
                'success' => true,
                'msg' => 'Message sent successfully',
                'activity' => $activityData
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => 'Something went wrong'
            ];
        }

        return $output;
    }

    /**
     * Update the ticket status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user = auth()->user();
            $business_id = request()->session()->get('user.business_id');
            $ticket = Ticket::whereHas('lead', function ($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })->findOrFail($id);

            $newStatus = $request->input('status');

            // Only admin can close tickets
            if ($newStatus === 'closed' && !$user->hasRole('Admin#' . $business_id)) {
                return [
                    'success' => false,
                    'msg' => 'Only administrators can close tickets'
                ];
            }

            // Track status change
            if ($ticket->status != $newStatus) {
                $updateData = ['status' => $newStatus];
                
                // If closing the ticket, record who closed it and when
                if ($newStatus === 'closed') {
                    $updateData['closed_by'] = auth()->user()->id;
                    $updateData['closed_at'] = now();
                }

                $activity = TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => auth()->user()->id,
                    'activity_type' => 'status_changed',
                    'activity_details' => 'Status changed from ' . ucfirst(str_replace('_', ' ', $ticket->status)) . ' to ' . ucfirst(str_replace('_', ' ', $newStatus))
                ]);

                $ticket->update($updateData);

                // Broadcast the status change via WebSocket
                broadcast(new TicketMessageEvent($activity->load('user')))->toOthers();
            }

            $output = [
                'success' => true,
                'msg' => 'Status updated successfully'
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => 'Something went wrong'
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $ticket = Ticket::whereHas('lead', function ($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })->findOrFail($id);
            
            $ticket->delete();

            $output = [
                'success' => true,
                'msg' => 'Success'
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => 'Something went wrong'
            ];
        }

        return $output;
    }
}

