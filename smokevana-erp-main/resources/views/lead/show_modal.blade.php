<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">@lang('lang_v1.lead_details')</h4>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -30px">
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print btn-modal"
                    data-href="{{ action([\App\Http\Controllers\LeadController::class, 'edit'], [$lead->id]) }}"
                    data-container=".lead_modal">
                    <i class="fa fa-edit"></i> @lang('messages.edit')
                </button>
                <button type="button" class="tw-dw-btn tw-dw-btn-danger tw-text-white no-print" data-dismiss="modal"
                    id='close_button'>@lang('messages.close')</button>
            </div>
        </div>
        <div class="modal-body" style="max-height: 85vh; overflow-y: auto;">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">@lang('lang_v1.basic_information')</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped">
                                <tr>
                                    <th width="35%">Reference No:</th>
                                    <td><strong>{{ $lead->reference_no ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>@lang('lang_v1.store_name'):</th>
                                    <td><strong>{{ $lead->store_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Company Name:</th>
                                    <td>{{ $lead->company_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Industry:</th>
                                    <td>{{ $lead->industry ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Company Size:</th>
                                    <td>{{ $lead->company_size ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Website:</th>
                                    <td>
                                        @if (!empty($lead->website))
                                            <a href="{{ $lead->website }}" target="_blank">{{ $lead->website }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Contact Information</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped">
                                <tr>
                                    <th width="35%">Contact Name:</th>
                                    <td>{{ $lead->contact_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Phone:</th>
                                    <td><strong>{{ $lead->contact_phone ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Contact Email:</th>
                                    <td>{{ $lead->contact_email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Preferred Contact:</th>
                                    <td>{{ ucfirst($lead->preferred_contact_method ?? 'phone') }}</td>
                                </tr>
                                @if ($lead->best_contact_time_start && $lead->best_contact_time_end)
                                    <tr>
                                        <th>Best Contact Time:</th>
                                        <td>{{ $lead->best_contact_time_start }} - {{ $lead->best_contact_time_end }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-map-marker"></i> Address Information (Google Address)
                            </h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped">
                                        <tr>
                                            <th width="20%">@lang('lang_v1.full_address'):</th>
                                            <td><strong>{{ $lead->full_address ?? 'N/A' }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <th width="40%">@lang('lang_v1.address_line_1'):</th>
                                            <td>{{ $lead->address_line_1 ?? 'N/A' }}</td>
                                        </tr>
                                        @if (!empty($lead->address_line_2))
                                            <tr>
                                                <th>@lang('lang_v1.address_line_2'):</th>
                                                <td>{{ $lead->address_line_2 }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>@lang('lang_v1.city'):</th>
                                            <td>{{ $lead->city ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <th width="40%">@lang('lang_v1.state'):</th>
                                            <td>{{ $lead->state ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('lang_v1.country'):</th>
                                            <td>{{ $lead->country ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('lang_v1.zip_code'):</th>
                                            <td>{{ $lead->zip_code ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @if ($lead->latitude && $lead->longitude)
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped">
                                            <tr>
                                                <th width="20%">GPS Coordinates:</th>
                                                <td>
                                                    <i class="fa fa-map-pin text-danger"></i>
                                                    Lat: {{ number_format($lead->latitude, 6) }},
                                                    Long: {{ number_format($lead->longitude, 6) }}
                                                    @if ($lead->location_accuracy)
                                                        <small class="text-muted">(Accuracy:
                                                            {{ $lead->location_accuracy }}m)</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lead Management & Status -->
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-success">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-tasks"></i> Lead Management</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped">
                                <tr>
                                    <th width="40%">Visit Status:</th>
                                    <td>
                                        <span
                                            class="label label-{{ $lead->status == 'visited' ? 'success' : 'warning' }}">
                                            {{ ucfirst($lead->status ?? 'pending') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lead Status:</th>
                                    <td>
                                        <span class="label label-info">
                                            {{ ucfirst(str_replace('_', ' ', $lead->lead_status ?? 'new')) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Priority:</th>
                                    <td>
                                        <span
                                            class="label label-{{ $lead->priority == 'urgent' ? 'danger' : ($lead->priority == 'high' ? 'warning' : 'default') }}">
                                            {{ ucfirst($lead->priority ?? 'medium') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lead Source:</th>
                                    <td>{{ ucfirst(str_replace('_', ' ', $lead->lead_source ?? 'N/A')) }}</td>
                                </tr>
                                <tr>
                                    <th>Funnel Stage:</th>
                                    <td>{{ ucfirst(str_replace('_', ' ', $lead->funnel_stage ?? 'N/A')) }}</td>
                                </tr>
                                @if ($lead->is_qualified)
                                    <tr>
                                        <th>Qualified:</th>
                                        <td><span class="label label-success"><i class="fa fa-check"></i> Yes</span>
                                        </td>
                                    </tr>
                                @endif
                                @if ($lead->is_hot_lead)
                                    <tr>
                                        <th>Hot Lead:</th>
                                        <td><span class="label label-danger"><i class="fa fa-fire"></i> Yes</span></td>
                                    </tr>
                                @endif
                                @if ($lead->requires_immediate_attention)
                                    <tr>
                                        <th>Urgent:</th>
                                        <td><span class="label label-warning"><i class="fa fa-exclamation-triangle"></i>
                                                Immediate Attention</span></td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-users"></i> Team Assignment</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped">
                                @if ($lead->salesRep)
                                    <tr>
                                        <th width="40%">Sales Rep:</th>
                                        <td>
                                            <strong>{{ $lead->salesRep->first_name }}
                                                {{ $lead->salesRep->last_name }}</strong>
                                            @if ($lead->salesRep->contact_number)
                                                <br><small class="text-muted"><i class="fa fa-phone"></i>
                                                    {{ $lead->salesRep->contact_number }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($lead->assignedTo)
                                    <tr>
                                        <th>Assigned To:</th>
                                        <td>
                                            <strong>{{ $lead->assignedTo->first_name }}
                                                {{ $lead->assignedTo->last_name }}</strong>
                                            @if ($lead->assignedTo->contact_number)
                                                <br><small class="text-muted"><i class="fa fa-phone"></i>
                                                    {{ $lead->assignedTo->contact_number }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>@lang('lang_v1.created_by'):</th>
                                    <td>{{ $lead->creator ? $lead->creator->first_name . ' ' . $lead->creator->last_name : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('lang_v1.created_at'):</th>
                                    <td>{{ @format_date($lead->created_at) }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('lang_v1.updated_at'):</th>
                                    <td>{{ @format_date($lead->updated_at) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Follow-up & Dates -->
            @if ($lead->next_follow_up_date || $lead->last_contact_date)
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-calendar"></i> Follow-up Schedule</h3>
                            </div>
                            <div class="box-body">
                                <table class="table table-striped">
                                    @if ($lead->next_follow_up_date)
                                        <tr>
                                            <th width="20%">Next Follow-up:</th>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($lead->next_follow_up_date)->format('M d, Y h:i A') }}</strong>
                                                @if (\Carbon\Carbon::parse($lead->next_follow_up_date)->isPast())
                                                    <span class="label label-danger">Overdue</span>
                                                @elseif(\Carbon\Carbon::parse($lead->next_follow_up_date)->isToday())
                                                    <span class="label label-warning">Due Today</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($lead->last_contact_date)
                                        <tr>
                                            <th>Last Contact:</th>
                                            <td>{{ \Carbon\Carbon::parse($lead->last_contact_date)->format('M d, Y h:i A') }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Lead Value & Scoring -->
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-dollar"></i> Lead Value & Scoring</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped">
                                <tr>
                                    <th width="40%">Estimated Value:</th>
                                    <td>
                                        @if ($lead->estimated_value)
                                            <strong>{{ $lead->currency ?? 'USD' }}
                                                {{ number_format($lead->estimated_value, 2) }}</strong>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @if ($lead->actual_value)
                                    <tr>
                                        <th>Actual Value:</th>
                                        <td><strong>{{ $lead->currency ?? 'USD' }}
                                                {{ number_format($lead->actual_value, 2) }}</strong></td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Lead Score:</th>
                                    <td>
                                        @if ($lead->lead_score)
                                            <strong>{{ $lead->lead_score }}/100</strong>
                                            <div class="progress" style="height: 10px; margin-top: 5px;">
                                                <div class="progress-bar progress-bar-{{ $lead->lead_score >= 70 ? 'success' : ($lead->lead_score >= 40 ? 'warning' : 'danger') }}"
                                                    style="width: {{ $lead->lead_score }}%"></div>
                                            </div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Rating:</th>
                                    <td>
                                        @if ($lead->rating)
                                            @for ($i = 1; $i <= $lead->rating; $i++)
                                                <i class="fa fa-star text-warning"></i>
                                            @endfor
                                            @for ($i = $lead->rating + 1; $i <= 5; $i++)
                                                <i class="fa fa-star-o text-muted"></i>
                                            @endfor
                                        @else
                                            <span class="text-muted">Not rated</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-info-circle"></i> Additional Details</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped">
                                @if ($lead->utm_source || $lead->utm_medium || $lead->utm_campaign)
                                    <tr>
                                        <th width="40%">UTM Tracking:</th>
                                        <td>
                                            @if ($lead->utm_source)
                                                <small><strong>Source:</strong> {{ $lead->utm_source }}</small><br>
                                            @endif
                                            @if ($lead->utm_medium)
                                                <small><strong>Medium:</strong> {{ $lead->utm_medium }}</small><br>
                                            @endif
                                            @if ($lead->utm_campaign)
                                                <small><strong>Campaign:</strong> {{ $lead->utm_campaign }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($lead->referral_source)
                                    <tr>
                                        <th>Referral Source:</th>
                                        <td>{{ $lead->referral_source }}</td>
                                    </tr>
                                @endif
                                @if ($lead->tags && is_array($lead->tags) && count($lead->tags) > 0)
                                    <tr>
                                        <th>Tags:</th>
                                        <td>
                                            @foreach ($lead->tags as $tag)
                                                <span class="label label-default">{{ $tag }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if ($lead->notes || $lead->internal_notes)
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-commenting"></i> Notes</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    @if ($lead->notes)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><i class="fa fa-file-text-o"></i> Public Notes:</label>
                                                <div class="well well-sm"
                                                    style="min-height: 80px; max-height: 150px; overflow-y: auto;">
                                                    {{ $lead->notes }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($lead->internal_notes)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><i class="fa fa-lock"></i> Internal Notes:</label>
                                                <div class="well well-sm"
                                                    style="min-height: 80px; max-height: 150px; overflow-y: auto; background-color: #fff3cd;">
                                                    {{ $lead->internal_notes }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tickets Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <i class="fa fa-ticket"></i> Support Tickets for {{ $lead->store_name }}
                                <span class="badge bg-blue">{{ $ticketStats['total'] }}</span>
                            </h3>
                            <div class="box-tools pull-right">
                                @if($ticketStats['open'] > 0)
                                    <span class="label label-danger">{{ $ticketStats['open'] }} Open</span>
                                @endif
                                @if($ticketStats['in_progress'] > 0)
                                    <span class="label label-warning">{{ $ticketStats['in_progress'] }} In Progress</span>
                                @endif
                                @if($ticketStats['pending'] > 0)
                                    <span class="label label-info">{{ $ticketStats['pending'] }} Pending</span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <!-- Lead Info Banner -->
                            <div class="alert alert-default" style="background-color: #f4f4f4; border-left: 4px solid #3c8dbc; margin-bottom: 15px;">
                                <i class="fa fa-filter"></i>
                                <strong>Filtered by Lead:</strong> {{ $lead->store_name }} ({{ $lead->reference_no }})
                                <span class="pull-right">
                                    <strong>Total Tickets:</strong> {{ $ticketStats['total'] }}
                                </span>
                            </div>

                            @if ($tickets->count() > 0)
                                <!-- Ticket Statistics Summary -->
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-sm-2">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $ticketStats['total'] }}</h5>
                                            <span class="description-text">TOTAL</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="description-block">
                                            <h5 class="description-header text-danger">{{ $ticketStats['open'] }}</h5>
                                            <span class="description-text">OPEN</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="description-block">
                                            <h5 class="description-header text-warning">{{ $ticketStats['in_progress'] }}</h5>
                                            <span class="description-text">IN PROGRESS</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="description-block">
                                            <h5 class="description-header text-info">{{ $ticketStats['pending'] }}</h5>
                                            <span class="description-text">PENDING</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="description-block">
                                            <h5 class="description-header text-primary">{{ $ticketStats['resolved'] }}</h5>
                                            <span class="description-text">RESOLVED</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="description-block">
                                            <h5 class="description-header text-success">{{ $ticketStats['closed'] }}</h5>
                                            <span class="description-text">CLOSED</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tickets List -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="12%">Ticket #</th>
                                                <th width="12%">For Lead</th>
                                                <th>Description</th>
                                                <th>Issue Type</th>
                                                <th>Priority</th>
                                                <th>Status</th>
                                                <th>Created By</th>
                                                <th>Created Date</th>
                                                <th>Image</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tickets as $ticket)
                                                <tr>
                                                    <td>
                                                        <strong class="text-primary">{{ $ticket->reference_no }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($ticket->lead)
                                                            <span class="text-success">
                                                                <i class="fa fa-check-circle"></i>
                                                                {{ $ticket->lead->reference_no }}
                                                            </span>
                                                            <br>
                                                            <small class="text-muted">{{ Str::limit($ticket->lead->store_name, 20) }}</small>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div style="max-width: 300px; white-space: normal;">
                                                            {{ $ticket->ticket_description }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="label label-default">
                                                            {{ ucfirst(str_replace('_', ' ', $ticket->issue_type)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $priorityClass = 'default';
                                                            $priorityIcon = '';
                                                            switch($ticket->issue_priority ?? 'medium') {
                                                                case 'urgent':
                                                                    $priorityClass = 'danger';
                                                                    $priorityIcon = 'fa fa-exclamation-triangle';
                                                                    break;
                                                                case 'high':
                                                                    $priorityClass = 'warning';
                                                                    $priorityIcon = 'fa fa-arrow-up';
                                                                    break;
                                                                case 'low':
                                                                    $priorityClass = 'info';
                                                                    $priorityIcon = 'fa fa-arrow-down';
                                                                    break;
                                                                default:
                                                                    $priorityClass = 'default';
                                                                    $priorityIcon = 'fa fa-minus';
                                                            }
                                                        @endphp
                                                        <span class="label label-{{ $priorityClass }}">
                                                            <i class="{{ $priorityIcon }}"></i>
                                                            {{ strtoupper($ticket->issue_priority ?? 'MEDIUM') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusClass = 'default';
                                                            switch($ticket->status) {
                                                                case 'open':
                                                                    $statusClass = 'danger';
                                                                    break;
                                                                case 'in_progress':
                                                                    $statusClass = 'warning';
                                                                    break;
                                                                case 'pending':
                                                                    $statusClass = 'info';
                                                                    break;
                                                                case 'resolved':
                                                                    $statusClass = 'primary';
                                                                    break;
                                                                case 'closed':
                                                                    $statusClass = 'success';
                                                                    break;
                                                            }
                                                        @endphp
                                                        <span class="label label-{{ $statusClass }}">
                                                            {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
                                                        </span>
                                                        @if($ticket->closed_at)
                                                            <br>
                                                            <small class="text-muted">
                                                                Closed: {{ \Carbon\Carbon::parse($ticket->closed_at)->format('M d, Y') }}
                                                            </small>
                                                            @if($ticket->closedBy)
                                                                <br>
                                                                <small class="text-muted">
                                                                    By: {{ $ticket->closedBy->first_name }} {{ $ticket->closedBy->last_name }}
                                                                </small>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($ticket->user)
                                                            <strong>{{ $ticket->user->first_name }} {{ $ticket->user->last_name }}</strong>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y') }}</strong><br>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('h:i A') }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($ticket->initial_image)
                                                            <a href="{{ url('uploads/tickets/' . $ticket->initial_image) }}" 
                                                               target="_blank" 
                                                               title="View Image">
                                                                <img src="{{ url('uploads/tickets/' . $ticket->initial_image) }}" 
                                                                     alt="Ticket Image" 
                                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                                                     class="img-thumbnail">
                                                            </a>
                                                        @else
                                                            <span class="text-muted">
                                                                <i class="fa fa-picture-o"></i>
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    No support tickets have been created for <strong>{{ $lead->store_name }}</strong> ({{ $lead->reference_no }}) yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visit Information Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">
                                <i class="fa fa-calendar-check-o"></i> Visit Information
                            </h3>
                        </div>
                        <div class="box-body">
                            @if ($visitRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sales Rep</th>
                                                <th>Visit Date & Time</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                                <th>Visit Type</th>
                                                <th>Proof Collected</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($visitRecords as $visit)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $visit->first_name }}
                                                            {{ $visit->last_name }}</strong><br>
                                                        <small class="text-muted">{{ $visit->username }}</small>
                                                    </td>
                                                    <td>
                                                        <strong>{{ \Carbon\Carbon::parse($visit->start_time)->format('M d, Y') }}</strong><br>
                                                        <small
                                                            class="text-muted">{{ \Carbon\Carbon::parse($visit->start_time)->format('h:i A') }}</small>
                                                    </td>
                                                    <td>
                                                        @if ($visit->duration)
                                                            {{ $visit->duration }} minutes
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="label label-{{ $visit->status == 'completed' ? 'success' : ($visit->status == 'in_progress' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($visit->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ ucfirst($visit->visit_type) }}</td>
                                                    <td>
                                                        <div class="btn-group-vertical btn-group-sm">
                                                            @if ($visit->location_proof)
                                                                <span class="badge badge-info">
                                                                    <i class="fa fa-map-marker"></i> Location
                                                                </span>
                                                            @endif
                                                            @if ($visit->photo_proof)
                                                                <span class="badge badge-success">
                                                                    <i class="fa fa-camera"></i> Photos
                                                                </span>
                                                            @endif
                                                            @if ($visit->signature_proof)
                                                                <span class="badge badge-warning">
                                                                    <i class="fa fa-pencil"></i> Signature
                                                                </span>
                                                            @endif
                                                            @if ($visit->video_proof)
                                                                <span class="badge badge-primary">
                                                                    <i class="fa fa-video-camera"></i> Video
                                                                </span>
                                                            @endif
                                                            @if (!$visit->location_proof && !$visit->photo_proof && !$visit->signature_proof && !$visit->video_proof)
                                                                <span class="text-muted">No proof collected</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    No visits have been recorded for this lead yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
