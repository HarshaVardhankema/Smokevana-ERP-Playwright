<div class="row">
    <!-- Shift Status -->
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body" style="padding: 15px;">
                <h4 style="margin-top: 0; font-size: 15px;">
                    <i class="fa fa-{{ $shiftInfo['is_online'] ? 'circle' : 'circle-o' }}" style="color: {{ $shiftInfo['is_online'] ? '#00a65a' : '#999' }};"></i>
                    <strong>Current Status: {{ $shiftInfo['status'] }}</strong>
                </h4>
                <div style="margin-top: 10px;">
                    @if($shiftInfo['is_online'])
                        <p style="margin: 3px 0; font-size: 13px;"><strong>Shift Started:</strong> {{ $shiftInfo['shift_start']->format('M d, Y h:i A') }}</p>
                        <p style="margin: 3px 0; font-size: 13px;"><strong>Duration:</strong> {{ $shiftInfo['duration'] }}</p>
                    @else
                        <p style="margin: 3px 0; font-size: 13px;"><strong>Status:</strong> Currently Offline</p>
                        @if($shiftInfo['last_shift_end'])
                            <p style="margin: 3px 0; font-size: 13px;"><strong>Last Shift Ended:</strong> {{ $shiftInfo['last_shift_end']->format('M d, Y h:i A') }}</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overview Stats -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Overview Statistics</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <!-- This Week -->
                    <div class="col-md-4">
                        <div class="info-box bg-white">
                            <span class="info-box-icon bg-gray"><i class="fa fa-calendar-check-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="font-size: 11px;">THIS WEEK</span>
                                <span class="info-box-number" style="font-size: 20px;">{{ $weekStats['visits'] }} <small style="font-size: 14px;">Visits</small></span>
                                <div class="progress" style="height: 3px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $weekStats['visits'] > 0 ? ($weekStats['completed'] / $weekStats['visits']) * 100 : 0 }}%;"></div>
                                </div>
                                <span class="progress-description" style="font-size: 12px;">
                                    {{ $weekStats['completed'] }} Completed | {{ $weekStats['leads'] }} Leads
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- This Month -->
                    <div class="col-md-4">
                        <div class="info-box bg-white">
                            <span class="info-box-icon bg-gray"><i class="fa fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="font-size: 11px;">THIS MONTH</span>
                                <span class="info-box-number" style="font-size: 20px;">{{ $monthStats['visits'] }} <small style="font-size: 14px;">Visits</small></span>
                                <div class="progress" style="height: 3px;">
                                    <div class="progress-bar bg-success" style="width: {{ $monthStats['visits'] > 0 ? ($monthStats['completed'] / $monthStats['visits']) * 100 : 0 }}%;"></div>
                                </div>
                                <span class="progress-description" style="font-size: 12px;">
                                    {{ $monthStats['completed'] }} Completed | {{ $monthStats['leads'] }} Leads
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Summary -->
                    <div class="col-md-4">
                        <div class="info-box bg-white">
                            <span class="info-box-icon bg-gray"><i class="fa fa-clock-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="font-size: 11px;">TODAY</span>
                                <span class="info-box-number" style="font-size: 20px;">{{ $todayVisits->count() }} <small style="font-size: 14px;">Visits</small></span>
                                <div class="progress" style="height: 3px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $todayVisits->count() > 0 ? ($todayVisits->where('status', 'completed')->count() / $todayVisits->count()) * 100 : 0 }}%;"></div>
                                </div>
                                <span class="progress-description" style="font-size: 12px;">
                                    {{ $todayVisits->where('status', 'completed')->count() }} Completed
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Visits -->
<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-size: 14px;">
                    <i class="fa fa-calendar-check-o"></i> Today's Visits ({{ $todayVisits->count() }})
                </h3>
            </div>
            <div class="box-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                @forelse($todayVisits as $visit)
                    <div style="border: 1px solid #ddd; border-radius: 3px; margin-bottom: 10px; padding: 10px; background-color: #f9f9f9;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <h5 style="margin: 0; font-size: 14px; font-weight: bold;">
                                <i class="fa fa-map-marker"></i>
                                {{ $visit->lead ? $visit->lead->store_name : 'N/A' }}
                            </h5>
                            <span class="label label-{{ $visit->status === 'completed' ? 'success' : ($visit->status === 'in_progress' ? 'warning' : 'default') }}" style="font-size: 10px;">
                                {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                            </span>
                        </div>
                        <p style="margin: 5px 0; font-size: 12px; color: #666;">
                            <i class="fa fa-clock-o"></i> {{ $visit->start_time->format('h:i A') }}
                            @if($visit->checkout_time)
                                - {{ $visit->checkout_time->format('h:i A') }}
                            @endif
                        </p>
                        @if($visit->lead)
                            <p style="margin: 5px 0; font-size: 12px; color: #666;">
                                <i class="fa fa-user"></i> {{ $visit->lead->contact_name ?? 'N/A' }}
                                @if($visit->lead->contact_phone)
                                    | <i class="fa fa-phone"></i> {{ $visit->lead->contact_phone }}
                                @endif
                            </p>
                        @endif
                        @if($visit->duration)
                            <p style="margin: 5px 0; font-size: 12px; color: #666;">
                                <i class="fa fa-hourglass-half"></i> {{ $visit->duration }}
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="text-center" style="padding: 40px 0; color: #999;">
                        <i class="fa fa-calendar-times-o" style="font-size: 36px; margin-bottom: 10px; opacity: 0.3;"></i>
                        <p style="font-size: 13px;">No visits today</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Yesterday's Visits -->
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-size: 14px;">
                    <i class="fa fa-history"></i> Yesterday's Visits ({{ $yesterdayVisits->count() }})
                </h3>
            </div>
            <div class="box-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                @forelse($yesterdayVisits as $visit)
                    <div style="border: 1px solid #ddd; border-radius: 3px; margin-bottom: 10px; padding: 10px; background-color: #f9f9f9;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <h5 style="margin: 0; font-size: 14px; font-weight: bold;">
                                <i class="fa fa-map-marker"></i>
                                {{ $visit->lead ? $visit->lead->store_name : 'N/A' }}
                            </h5>
                            <span class="label label-{{ $visit->status === 'completed' ? 'success' : 'default' }}" style="font-size: 10px;">
                                {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                            </span>
                        </div>
                        <p style="margin: 5px 0; font-size: 12px; color: #666;">
                            <i class="fa fa-clock-o"></i> {{ $visit->start_time->format('h:i A') }}
                            @if($visit->checkout_time)
                                - {{ $visit->checkout_time->format('h:i A') }}
                            @endif
                        </p>
                        @if($visit->lead)
                            <p style="margin: 5px 0; font-size: 12px; color: #666;">
                                <i class="fa fa-user"></i> {{ $visit->lead->contact_name ?? 'N/A' }}
                                @if($visit->lead->contact_phone)
                                    | <i class="fa fa-phone"></i> {{ $visit->lead->contact_phone }}
                                @endif
                            </p>
                        @endif
                        @if($visit->duration)
                            <p style="margin: 5px 0; font-size: 12px; color: #666;">
                                <i class="fa fa-hourglass-half"></i> {{ $visit->duration }}
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="text-center" style="padding: 40px 0; color: #999;">
                        <i class="fa fa-calendar-times-o" style="font-size: 36px; margin-bottom: 10px; opacity: 0.3;"></i>
                        <p style="font-size: 13px;">No visits yesterday</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Link to Full Visit History -->
<div class="row">
    <div class="col-md-12 text-center" style="margin-top: 15px; margin-bottom: 15px;">
        <a href="{{ route('visit-history.index') }}?sales_rep_id={{ $user->id }}" class="btn btn-primary" style="padding: 8px 20px; font-size: 13px;">
            <i class="fa fa-history"></i> View Complete Visit History
        </a>
    </div>
</div>

