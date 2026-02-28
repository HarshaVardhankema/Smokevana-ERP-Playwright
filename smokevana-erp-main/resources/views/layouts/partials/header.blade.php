@inject('request', 'Illuminate\Http\Request')
<!-- Main Header -->

<div
id="header_main_app"
    class="tw-transition-all tw-duration-5000 tw-border-b tw-bg-gradient-to-r tw-from-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 tw-to-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-900 tw-shrink-0 lg:tw-h-15 tw-border-primary-500/30 tw-relative tw-z-50 no-print">
    <div class="tw-px-5 tw-py-3">
        <div class="tw-flex tw-items-start tw-justify-between tw-gap-6 lg:tw-items-center">
            <div class="tw-flex tw-items-center tw-gap-2">
                <button type="button" 
                    class="small-view-button xl:tw-w-20 lg:tw-hidden tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-p-1.5 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10">
                    <span class="tw-sr-only">
                        Sidebar Menu
                    </span>
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 6l16 0" />
                        <path d="M4 12l16 0" />
                        <path d="M4 18l16 0" />
                    </svg>
                </button>

                <button type="button"
                    class="side-bar-collapse tw-hidden lg:tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-p-1.5 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10">
                    <span class="tw-sr-only">
                        Collapse Sidebar
                    </span>
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                        <path d="M15 4v16" />
                        <path d="M10 10l-2 2l2 2" />
                    </svg>
                </button>

                {{-- Browser Navigation Buttons --}}
                <button type="button" onclick="window.history.back()"
                    class="tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-p-1.5 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10"
                    title="Go Back">
                    <span class="tw-sr-only">
                        Go Back
                    </span>
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M5 12l14 0" />
                        <path d="M5 12l6 6" />
                        <path d="M5 12l6 -6" />
                    </svg>
                </button>

                {{-- Refresh Button --}}
                <button type="button" onclick="window.location.reload()"
                    class="tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-p-1.5 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10"
                    title="Refresh Page">
                    <span class="tw-sr-only">
                        Refresh Page
                    </span>
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-2.5 -4v4h4" />
                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m2.5 4v-4h-4" />
                    </svg>
                </button>

                <button type="button" onclick="window.history.forward()"
                    class="tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-p-1.5 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10"
                    title="Go Forward">
                    <span class="tw-sr-only">
                        Go Forward
                    </span>
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M5 12l14 0" />
                        <path d="M13 18l6 -6" />
                        <path d="M13 6l6 6" />
                    </svg>
                </button>

                {{-- @if(auth()->user()->can('navigation_page_access')&& !auth()->user()->can('access_all_locations'))
                <button type="button" onclick="window.location.href='{{ action([\App\Http\Controllers\HomeController::class, 'navigationPage']) }}'"
                    class="tw-hidden lg:tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-primary-800 hover:tw-bg-primary-700 tw-p-1.5 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M3 7l1 -4h16l1 4z" /> <!-- Roof -->
                    <path d="M5 7v13h14v-13" /> <!-- Walls -->
                    <path d="M9 14h6v6h-6z" /> <!-- Door -->
                </svg>
                </button>
                @endif
                @php
                    $location=auth()->user()->permitted_locations();
                @endphp
                @if($location == 'all')
                    <input type="text" value="All Locations" disabled 
                           class="tw-text-sm tw-font-medium tw-text-gray-600 tw-bg-gray-100 tw-border tw-border-gray-300 tw-rounded-lg tw-px-3 tw-py-1.5 tw-cursor-not-allowed tw-opacity-60">
                @else
                    @php
                        $location_name = \App\BusinessLocation::find($location[0])->name ?? 'Unknown Location';
                    @endphp
                    <input type="text" value="{{ $location_name }}" disabled 
                           class="tw-text-sm tw-font-medium tw-text-gray-600 tw-bg-gray-100 tw-border tw-border-gray-300 tw-rounded-lg tw-px-3 tw-py-1.5 tw-cursor-not-allowed tw-opacity-60">
                @endif --}}
            </div>

            {{-- Search Bar --}}
            <div class="tw-flex-1 tw-mx-4 tw-min-w-[200px] sm:tw-min-w-[280px] tw-max-w-xl tw-relative tw-z-50">
                <div class="tw-relative tw-w-full tw-flex tw-items-center tw-gap-2 tw-border tw-rounded-lg tw-bg-white focus-within:tw-ring-2 focus-within:tw-ring-primary-500 focus-within:tw-border-primary-500">
                    <span class="tw-ml-4 tw-mr-1 tw-flex tw-items-center tw-flex-shrink-0" aria-hidden="true">
                        <svg class="tw-h-5 tw-w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#111111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </span>
                    <input type="text" 
                        id="dashboard_search" 
                        class="tw-flex-1 tw-min-w-0 tw-py-2 tw-pr-4 tw-pl-0 tw-border-0 tw-bg-transparent tw-text-sm tw-text-gray-900 placeholder:tw-text-gray-400 focus:tw-outline-none focus:tw-ring-0" 
                        placeholder="@lang('lang_v1.search')... (Invoice, Customer, Product)"
                        autocomplete="off">
                    <span id="header_bookmark_star" class="tw-ml-1 tw-mr-6 tw-flex tw-items-center tw-cursor-pointer" role="button" title="@lang('home.bookmark_this_page')" tabindex="0">
                        <svg class="tw-h-5 tw-w-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                        </svg>
                    </span>
                    <div id="dashboard_search_results" class="tw-mt-1 tw-bg-white tw-border tw-border-gray-200 tw-rounded-lg tw-shadow-2xl tw-max-h-96 tw-overflow-y-auto tw-min-w-[320px] dashboard-search-results" style="display: none; position: fixed; z-index: 999999;"></div>
                </div>
            </div>
            <style>
                /* Prevent search dropdown result subtitle (e.g. "Menu") from being truncated */
                #dashboard_search_results .dashboard-search-item {
                    min-height: 3.25rem;
                    overflow: visible;
                }
                #dashboard_search_results .dashboard-search-subtitle {
                    line-height: 1.4;
                    min-height: 1.25rem;
                    overflow: visible;
                }
            </style>
            {{-- Quick Actions (thunder) – left-side panel, Canva-style --}}
                <button type="button" id="quick_actions_btn" title="@lang('home.quick_actions')"
                    class="tw-hidden md:tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-p-1.5 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10">
                    <span class="tw-sr-only">@lang('home.quick_actions')</span>
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="currentColor" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11z" />
                    </svg>
                </button>
            {{-- Showing active package for SaaS Superadmin --}}
            @if(Module::has('Superadmin'))
                @includeIf('superadmin::layouts.partials.active_subscription')
            @endif

            {{-- When using superadmin, this button is used to switch users --}}
            @if(!empty(session('previous_user_id')) && !empty(session('previous_username')))
                <a href="{{route('sign-in-as-user', session('previous_user_id'))}}" class="btn btn-flat btn-danger m-8 btn-sm mt-10"><i class="fas fa-undo"></i> @lang('lang_v1.back_to_username', ['username' => session('previous_username')] )</a>
            @endif

            <div class="tw-flex tw-flex-wrap tw-items-center tw-justify-end tw-gap-2">
                @if(auth()->user()->can('navigation_page_access')&& !auth()->user()->can('access_all_locations'))
                @php
                    $permitted_locations = auth()->user()->permitted_locations();
                    $current_location_id = session('user.current_location_id', (is_array($permitted_locations) && !empty($permitted_locations)) ? $permitted_locations[0] : null);
                    
                    // Get all business locations (first 2) for toggling
                    $business_id = session('business.id', auth()->user()->business_id);
                    $all_locations = \App\BusinessLocation::where('business_id', $business_id)
                        ->where('is_active', 1)
                        ->orderBy('id')
                        ->limit(2)
                        ->pluck('id')
                        ->toArray();
                    
                    // Ensure we have at least 2 locations for toggling
                    $locations_array = count($all_locations) >= 2 ? array_slice($all_locations, 0, 2) : [];
                    $locations_json = htmlspecialchars(json_encode($locations_array), ENT_QUOTES, 'UTF-8');
                @endphp
                <button type="button" id="toggle-location-btn" 
                    data-permitted-locations="{{ $locations_json }}"
                    data-current-location-id="{{ $current_location_id }}"
                    class="tw-hidden lg:tw-inline-flex tw-items-center tw-gap-2 tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-primary-800 hover:tw-bg-primary-700 tw-py-2 tw-px-4 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10"
                    title="Switch Location">
                   Switch to @if($current_location_id == 1) B2C @else B2B @endif
                </button>
                @endif

                {{-- Dashboard Stats Button --}}
                @if(auth()->user()->can('direct_sell.view') || auth()->user()->can('sell.view'))
                <div class="tw-relative">
                    <button type="button" id="dashboard_stats_btn" 
                        class="tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-blue-600 hover:tw-bg-blue-700 tw-p-2 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10 tw-relative"
                        title="Order Statistics">
                        <span class="tw-sr-only">Order Statistics</span>
                          <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 7a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-10z" />
                            <path d="M3 7l9 6l9 -6" />
                            <path d="M12 20v-6" />
                        </svg>
                    </button>
                    
                    <!-- Dashboard Stats Dropdown -->
                    <div id="dashboard_stats_dropdown" class="tw-fixed tw-top-16 tw-right-4 tw-mt-2 tw-w-64 tw-bg-white tw-rounded-lg tw-shadow-lg tw-ring-1 tw-ring-gray-200 tw-hidden" style="position: absolute; z-index: 99999;">
                        <div class="tw-p-3">
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                <h3 class="tw-text-sm tw-font-semibold tw-text-gray-900">Today's Sales</h3>
                                <span class="tw-text-xs tw-text-gray-500" id="stats_datetime">{{ now()->format('M j, Y') }}</span>
                            </div>
                            
                            <!-- Order Status Counts -->
                            <div class="tw-mb-3">
                                <h4 class="tw-text-[11px] tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider tw-mb-2">Today's Stats</h4>
                                <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                                    <div class="tw-bg-yellow-50 tw-rounded-lg tw-p-2 tw-border tw-border-yellow-100">
                                        <div class="tw-flex tw-items-center">

                                            <div>
                                                <p class="tw-text-[11px] tw-text-gray-500">Processing</p>
                                                <p class="tw-text-base tw-font-bold tw-text-gray-900" id="processing_count">-</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tw-bg-blue-50 tw-rounded-lg tw-p-2 tw-border tw-border-blue-100">
                                        <div class="tw-flex tw-items-center">
                                          
                                            <div>
                                                <p class="tw-text-[11px] tw-text-gray-500">Picking</p>
                                                <p class="tw-text-base tw-font-bold tw-text-gray-900" id="picking_count">-</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tw-bg-green-50 tw-rounded-lg tw-p-2 tw-border tw-border-green-100">
                                        <div class="tw-flex tw-items-center">
                                           
                                            <div>
                                                <p class="tw-text-[11px] tw-text-gray-500">Completed</p>
                                                <p class="tw-text-base tw-font-bold tw-text-gray-900" id="completed_count">-</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tw-bg-purple-50 tw-rounded-lg tw-p-2 tw-border tw-border-purple-100">
                                        <div class="tw-flex tw-items-center">
                                          
                                            <div>
                                                <p class="tw-text-[11px] tw-text-gray-500">Total Sales</p>
                                                <p class="tw-text-base tw-font-bold tw-text-gray-900" id="total_sales_count">-</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Links -->
                            <div class="tw-flex tw-gap-2 tw-mt-3 tw-pt-3 tw-border-t">
                                <button onclick="window.location.href='{{ action([\App\Http\Controllers\OrderfulfillmentController::class, 'index']) }}'" 
                                    class="tw-flex-1 tw-bg-blue-500 hover:tw-bg-blue-600 tw-text-white tw-text-xs tw-font-medium tw-py-1.5 tw-px-2 tw-rounded-lg tw-transition-colors">
                                    View Orders
                                </button>
                                <button onclick="window.location.href='{{ action([\App\Http\Controllers\SellController::class, 'index']) }}'" 
                                    class="tw-flex-1 tw-bg-gray-500 hover:tw-bg-gray-600 tw-text-white tw-text-xs tw-font-medium tw-py-1.5 tw-px-2 tw-rounded-lg tw-transition-colors">
                                    View Sales
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                @if (Module::has('Essentials'))
                    @includeIf('essentials::layouts.partials.header_part')
                @endif

                {{-- Daily Order Reminder Button --}}
                @if(auth()->user()->can('direct_sell.view') || auth()->user()->can('sell.view'))
                <div class="tw-relative">
                    
                    <!-- Daily Order Reminder Dropdown -->
                    <div id="daily_reminder_dropdown" class="tw-fixed tw-top-16 tw-right-4 tw-mt-2 tw-w-80 tw-bg-white tw-rounded-lg tw-shadow-lg tw-ring-1 tw-ring-gray-200 tw-hidden" style="z-index: 9998;">
                        <div class="tw-p-4">
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                                <h3 class="tw-text-sm tw-font-semibold tw-text-gray-900">Daily Order Reminder</h3>
                                <span class="tw-text-xs tw-text-gray-500" id="reminder_date">{{ now()->format('M j, Y') }}</span>
                            </div>
                            
                            <!-- Remaining Orders Section -->
                            <div class="tw-mb-4">
                                <div class="tw-flex tw-items-center tw-mb-2">
                                    <svg class="tw-w-4 tw-h-4 tw-text-orange-500 tw-mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <h4 class="tw-text-sm tw-font-medium tw-text-gray-900">Remaining Orders to Complete</h4>
                                </div>
                                <div id="remaining_orders_list" class="tw-space-y-2 tw-max-h-32 tw-overflow-y-auto">
                                    <div class="tw-text-xs tw-text-gray-500">Loading remaining orders...</div>
                                </div>
                            </div>
                            
                            <!-- New Orders Section -->
                            <div class="tw-mb-4">
                                <div class="tw-flex tw-items-center tw-mb-2">
                                    <svg class="tw-w-4 tw-h-4 tw-text-green-500 tw-mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <h4 class="tw-text-sm tw-font-medium tw-text-gray-900">New Orders While Away</h4>
                                </div>
                                <div id="new_orders_list" class="tw-space-y-2 tw-max-h-32 tw-overflow-y-auto">
                                    <div class="tw-text-xs tw-text-gray-500">Loading new orders...</div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="tw-flex tw-gap-2 tw-mt-4 tw-pt-3 tw-border-t">
                                <button onclick="window.location.href='{{ action([\App\Http\Controllers\SellController::class, 'index']) }}?filter=remaining'" 
                                    class="tw-flex-1 tw-bg-orange-500 hover:tw-bg-orange-600 tw-text-white tw-text-xs tw-font-medium tw-py-2 tw-px-3 tw-rounded-lg tw-transition-colors">
                                    View Remaining
                                </button>
                                <button onclick="window.location.href='{{ action([\App\Http\Controllers\SellController::class, 'index']) }}?filter=new'" 
                                    class="tw-flex-1 tw-bg-green-500 hover:tw-bg-green-600 tw-text-white tw-text-xs tw-font-medium tw-py-2 tw-px-3 tw-rounded-lg tw-transition-colors">
                                    View New
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <details class="tw-dw-dropdown tw-relative tw-inline-block tw-text-left">
                    <summary
                        class="tw-inline-flex tw-transition-all tw-ring-1 tw-ring-white/10 hover:tw-text-white tw-cursor-pointer tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-py-1.5 tw-px-3 tw-rounded-lg tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white hover:tw-text-white tw-gap-1">
                        <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                            <path d="M9 12h6" />
                            <path d="M12 9v6" />
                        </svg>
                    </summary>
                    <ul class="tw-dw-menu tw-dw-dropdown-content tw-dw-z-[1] tw-dw-bg-base-100 tw-dw-rounded-box tw-w-48 tw-absolute tw-left-0 tw-z-10 tw-mt-2 tw-origin-top-right tw-bg-white tw-rounded-lg tw-shadow-lg tw-ring-1 tw-ring-gray-200 focus:tw-outline-none"
                        role="menu" tabindex="-1">
                        <div class="tw-p-2" role="none">
                            <a href="{{ route('calendar') }}"
                                class="tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-600 tw-transition-all tw-duration-200 tw-rounded-lg hover:tw-text-gray-900 hover:tw-bg-gray-100"
                                role="menuitem" tabindex="-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <rect x="4" y="5" width="16" height="16" rx="2" />
                                    <line x1="16" y1="3" x2="16" y2="7" />
                                    <line x1="8" y1="3" x2="8" y2="7" />
                                    <line x1="4" y1="11" x2="20" y2="11" />
                                    <line x1="11" y1="15" x2="12" y2="15" />
                                    <line x1="12" y1="15" x2="12" y2="18" />
                                </svg>
                                @lang('lang_v1.calendar')
                            </a>
                            @if (Module::has('Essentials'))
                                <a href="#"
                                    data-href="{{ action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'create']) }}"
                                    data-container="#task_modal"
                                    class="btn-modal tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-600 tw-transition-all tw-duration-200 tw-rounded-lg hover:tw-text-gray-900 hover:tw-bg-gray-100"
                                    role="menuitem" tabindex="-1">
                                    <svg aria-hidden="true" class="tw-w-5 tw-h-5" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                        <path d="M9 12l2 2l4 -4" />
                                    </svg>
                                    @lang('essentials::lang.add_to_do')
                                </a>
                            @endif
                            @if (auth()->user()->hasRole('Admin#' . auth()->user()->business_id))
                                <a href="#" id="start_tour"
                                    class="tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-600 tw-transition-all tw-duration-200 tw-rounded-lg hover:tw-text-gray-900 hover:tw-bg-gray-100"
                                    role="menuitem" tabindex="-1">
                                    <svg aria-hidden="true" class="tw-w-5 tw-h-5" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M12 17l0 .01" />
                                        <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4" />
                                    </svg>
                                    Application Tour
                                </a>
                            @endif
                        </div>
                    </ul>

                </details>


                {{-- data-toggle="popover" remove this for on hover show --}}

                <button id="btnCalculator" title="@lang('lang_v1.calculator')" data-content='@include('layouts.partials.calculator')'
                    type="button" data-trigger="click" data-html="true" data-placement="bottom" 
                    class="tw-hidden md:tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-p-1.5 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10">
                    <span class="tw-sr-only" aria-hidden="true">
                        Calculator
                    </span>
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                        <path d="M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" />
                        <path d="M8 14l0 .01" />
                        <path d="M12 14l0 .01" />
                        <path d="M16 14l0 .01" />
                        <path d="M8 17l0 .01" />
                        <path d="M12 17l0 .01" />
                        <path d="M16 17l0 .01" />
                    </svg>
                </button>

                <!-- {{-- Quick Actions (thunder) – left-side panel, Canva-style --}}
                <button type="button" id="quick_actions_btn" title="@lang('home.quick_actions')"
                    class="tw-hidden md:tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-p-1.5 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10">
                    <span class="tw-sr-only">@lang('home.quick_actions')</span>
                    <svg class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M13 3l0 7l4 0l-4 7l4 -4l-4 -4l4 0l0 -6z" />
                    </svg>
                </button> -->

                {{-- @if (in_array('pos_sale', $enabled_modules)) // erp disable
                    @can('sell.create')
                        <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}"
                            class="sm:tw-inline-flex tw-transition-all tw-duration-200 tw-gap-2 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-py-1.5 tw-px-3 tw-rounded-lg tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-ring-1 tw-ring-white/10 hover:tw-text-white tw-text-white">
                            <svg aria-hidden="true" class="tw-size-5 tw-hidden md:tw-block" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                <path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                <path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                <path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                            </svg>
                            @lang('sale.pos_sale')
                        </a>
                    @endcan
                @endif --}}
                @if (Module::has('Repair'))
                    @includeIf('repair::layouts.partials.header')
                @endif
                @can('profit_loss_report.view')
                    <button type="button" type="button" id="view_todays_profit" title="{{ __('home.todays_profit') }}"
                        data-toggle="tooltip" data-placement="bottom"
                        class="tw-hidden sm:tw-inline-flex tw-items-center tw-ring-1 tw-ring-white/10 tw-justify-center tw-text-sm tw-font-medium tw-text-white hover:tw-text-white tw-transition-all tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-p-1.5 tw-rounded-lg">
                        <span class="tw-sr-only">
                            Today's Profit
                        </span>
                        <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                            <path d="M18 12l.01 0" />
                            <path d="M6 12l.01 0" />
                        </svg>
                    </button>
                @endcan

                <button type="button"
                    class="tw-hidden lg:tw-inline-flex tw-transition-all tw-ring-1 tw-ring-white/10 tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-py-1.5 tw-px-3 tw-rounded-lg tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white hover:tw-text-white tw-font-mono">
                    {{ @format_date('now') }}
                </button>

                @include('layouts.partials.header-notifications')



                <details class="tw-dw-dropdown tw-relative tw-inline-block tw-text-left">
                    <summary data-toggle="popover"
                        class="tw-dw-m-1 tw-inline-flex tw-transition-all tw-ring-1 tw-ring-white/10 tw-cursor-pointer tw-duration-200 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 hover:tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-700 tw-py-1.5 tw-px-3 tw-rounded-lg tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white hover:tw-text-white tw-gap-1">
                        <span class="tw-hidden md:tw-block">{{ Auth::User()->first_name }} {{ Auth::User()->last_name }}</span>

                        <svg  xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="tw-size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>

                        
                        
                    </summary>

                    <ul class="tw-p-2 tw-w-48 tw-absolute tw-right-0 tw-z-10 tw-mt-2 tw-origin-top-right tw-rounded-lg tw-shadow-lg tw-ring-1 tw-ring-gray-200 focus:tw-outline-none"
                        style="background-color: #37475A;" role="menu" tabindex="-1">
                        <div class="tw-px-4 tw-pt-3 tw-pb-1" role="none">
                            <p class="tw-text-sm tw-text-gray-300" role="none">
                                Signed in as
                            </p>
                            <p class="tw-text-sm tw-font-medium tw-text-white tw-truncate" role="none">
                                {{ Auth::User()->first_name }} {{ Auth::User()->last_name }}
                            </p>
                        </div>

                        <li>
                            <a href="{{ action([\App\Http\Controllers\UserController::class, 'getProfile']) }}"
                                class="tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-rounded-lg hover:tw-text-white hover:tw-bg-gray-600"
                                role="menuitem" tabindex="-1">
                                <svg aria-hidden="true" class="tw-w-5 tw-h-5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
                                </svg>
                                @lang('lang_v1.profile')
                            </a>
                        </li>
                        <li>
                            <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}"
                                class="tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-rounded-lg hover:tw-text-white hover:tw-bg-gray-600"
                                role="menuitem" tabindex="-1">
                                <svg aria-hidden="true" class="tw-w-5 tw-h-5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                    <path d="M9 12h12l-3 -3" />
                                    <path d="M18 15l3 -3" />
                                </svg>
                                @lang('lang_v1.sign_out')
                            </a>
                        </li>
                    </ul>
                </details>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for jQuery to load
function waitForJQuery(callback) {
    if (typeof $ !== 'undefined' && $.fn && $.fn.jquery) {
        callback();
    } else {
        setTimeout(function() { waitForJQuery(callback); }, 100);
    }
}

waitForJQuery(function() {
$(document).ready(function() {
    function updateDailyOrderReminder() {
        $.ajax({
            url: '/sells/daily-order-reminder',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    // Update badge count
                    $('#reminder_badge').text(data.total_count);
                    
                    // Show/hide badge based on count
                    if (data.total_count > 0) {
                        $('#reminder_badge').removeClass('tw-hidden');
                        $('#daily_order_reminder_btn').addClass('tw-animate-pulse');
                    } else {
                        $('#reminder_badge').addClass('tw-hidden');
                        $('#daily_order_reminder_btn').removeClass('tw-animate-pulse');
                    }
                    
                    // Update remaining orders list
                    if (data.remaining_orders && data.remaining_orders.length > 0) {
                        let remainingHtml = '';
                        data.remaining_orders.forEach(function(order) {
                            remainingHtml += `
                                <div class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-bg-orange-50 tw-rounded tw-border tw-border-orange-200">
                                    <div class="tw-flex-1">
                                        <div class="tw-text-xs tw-font-medium tw-text-gray-900">${order.invoice_no}</div>
                                        <div class="tw-text-xs tw-text-gray-600">${order.customer}</div>
                                        <div class="tw-text-xs tw-text-gray-500">${order.location} • ${order.date}</div>
                                    </div>
                                    <div class="tw-text-right">
                                        <div class="tw-text-xs tw-font-medium tw-text-gray-900">$${order.amount}</div>
                                        <div class="tw-text-xs tw-text-orange-600">${order.status}</div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#remaining_orders_list').html(remainingHtml);
                    } else {
                        $('#remaining_orders_list').html('<div class="tw-text-xs tw-text-gray-500">No remaining orders</div>');
                    }
                    
                    // Update new orders list
                    if (data.new_orders && data.new_orders.length > 0) {
                        let newHtml = '';
                        data.new_orders.forEach(function(order) {
                            newHtml += `
                                <div class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-bg-green-50 tw-rounded tw-border tw-border-green-200">
                                    <div class="tw-flex-1">
                                        <div class="tw-text-xs tw-font-medium tw-text-gray-900">${order.invoice_no}</div>
                                        <div class="tw-text-xs tw-text-gray-600">${order.customer}</div>
                                        <div class="tw-text-xs tw-text-gray-500">${order.location} • ${order.created_at}</div>
                                    </div>
                                    <div class="tw-text-right">
                                        <div class="tw-text-xs tw-font-medium tw-text-gray-900">$${order.amount}</div>
                                        <div class="tw-text-xs tw-text-green-600">${order.status}</div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#new_orders_list').html(newHtml);
                    } else {
                        $('#new_orders_list').html('<div class="tw-text-xs tw-text-gray-500">No new orders</div>');
                    }
                    
                    console.log('Daily order reminder updated successfully');
                } else {
                    console.log('Invalid daily reminder response:', response);
                    $('#remaining_orders_list').html('<div class="tw-text-xs tw-text-red-500">Error loading data</div>');
                    $('#new_orders_list').html('<div class="tw-text-xs tw-text-red-500">Error loading data</div>');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error fetching daily order reminder:', error);
                console.log('Status:', status);
                console.log('Response:', xhr.responseText);
                
                $('#remaining_orders_list').html('<div class="tw-text-xs tw-text-red-500">Error loading data</div>');
                $('#new_orders_list').html('<div class="tw-text-xs tw-text-red-500">Error loading data</div>');
            }
        });
    }

    // Dropdown functionality for daily reminder
    $('#daily_order_reminder_btn').hover(
        function() {
            $('#daily_reminder_dropdown').removeClass('tw-hidden');
        },
        function() {
            setTimeout(function() {
                if (!$('#daily_reminder_dropdown').is(':hover')) {
                    $('#daily_reminder_dropdown').addClass('tw-hidden');
                }
            }, 200);
        }
    );

    // Keep dropdown open when hovering over it
    $('#daily_reminder_dropdown').hover(
        function() {
            $(this).removeClass('tw-hidden');
        },
        function() {
            $(this).addClass('tw-hidden');
        }
    );

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#daily_order_reminder_btn, #daily_reminder_dropdown').length) {
            $('#daily_reminder_dropdown').addClass('tw-hidden');
        }
    });

    // Update immediately on page load
    updateDailyOrderReminder();
    
    // Update every 5 minutes
    setInterval(updateDailyOrderReminder, 300000);
    
    // Update when window gains focus
    $(window).on('focus', function() {
        updateDailyOrderReminder();
    });
    
    // Dashboard Stats Button functionality
    function updateDashboardStats() {
        $.ajax({
            url: '/sells/get-order-stats',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    // Update counts
                    $('#processing_count').text(data.processing_count || 0);
                    $('#picking_count').text(data.picking_count || 0);
                    $('#completed_count').text(data.completed_count || 0);
                    $('#total_sales_count').text(data.total_sales_count || 0);
                } else {
                    $('#processing_count').text('-');
                    $('#picking_count').text('-');
                    $('#completed_count').text('-');
                    $('#total_sales_count').text('-');
                }
            },
            error: function() {
                $('#processing_count').text('-');
                $('#picking_count').text('-');
                $('#completed_count').text('-');
                $('#total_sales_count').text('-');
            }
        });
    }
    
    // Dropdown functionality for dashboard stats
    $('#dashboard_stats_btn').on('click', function(e) {
        e.stopPropagation();
        $('#dashboard_stats_dropdown').toggleClass('tw-hidden');
        if (!$('#dashboard_stats_dropdown').hasClass('tw-hidden')) {
            updateDashboardStats();
        }
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#dashboard_stats_btn, #dashboard_stats_dropdown').length) {
            $('#dashboard_stats_dropdown').addClass('tw-hidden');
        }
    });
    
    // Initial load
    updateDashboardStats();
});
}); // End waitForJQuery
</script>
