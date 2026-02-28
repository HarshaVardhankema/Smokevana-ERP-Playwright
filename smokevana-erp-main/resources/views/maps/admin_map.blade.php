@extends('layouts.app')

@section('title', __('Maps - Admin View'))

@section('content')
    <section class="content-header">
        <h1>@lang('Maps - Business Overview')</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('Map View')</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Filters -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Sales Rep')</label>
                                    <select class="form-control" id="filter_sales_rep">
                                        <option value="">@lang('All Sales Reps')</option>
                                        @foreach ($salesReps as $rep)
                                            <option value="{{ $rep->id }}">{{ $rep->first_name }} {{ $rep->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <select class="form-control" id="filter_status">
                                        <option value="">@lang('All Statuses')</option>
                                        <option value="new">@lang('New')</option>
                                        <option value="contacted">@lang('Contacted')</option>
                                        <option value="qualified">@lang('Qualified')</option>
                                        <option value="proposal">@lang('Proposal')</option>
                                        <option value="negotiation">@lang('Negotiation')</option>
                                        <option value="won">@lang('Won')</option>
                                        <option value="lost">@lang('Lost')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Date From')</label>
                                    <input type="date" class="form-control" id="filter_date_from">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Date To')</label>
                                    <input type="date" class="form-control" id="filter_date_to">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-primary" id="apply_filters">
                                    <i class="fa fa-filter"></i> @lang('Apply Filters')
                                </button>
                                <button class="btn btn-default" id="clear_filters">
                                    <i class="fa fa-times"></i> @lang('Clear')
                                </button>
                            </div>
                        </div>

                        <hr>

                        <!-- Legend -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="map-legend">
                                    <strong>@lang('Legend:')</strong>
                                    <span class="legend-item"><i class="fa fa-map-marker text-blue"></i>
                                        @lang('Leads')</span>
                                    <span class="legend-item"><i class="fa fa-map-marker text-green"></i>
                                        @lang('Visits')</span>
                                    <span class="legend-item"><i class="fa fa-map-marker text-orange"></i>
                                        @lang('Nearby Stores')</span>
                                    <span class="legend-item"><i class="fa fa-map-marker text-red"></i>
                                        @lang('Business Locations')</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Map Container -->
                        <div class="row">
                            <div class="col-md-12">
                                <div id="map" style="height: 600px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-map-marker"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('Total Leads')</span>
                        <span class="info-box-number" id="total_leads">{{ $leads->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('Total Visits')</span>
                        <span class="info-box-number" id="total_visits">{{ $visits->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-orange"><i class="fa fa-building"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('Nearby Stores')</span>
                        <span class="info-box-number" id="total_nearby_stores">{{ $nearbyStores->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-location-arrow"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('Locations')</span>
                        <span class="info-box-number">{{ $locations->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .map-legend {
            padding: 10px;
            background: #f4f4f4;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .legend-item {
            margin-right: 20px;
        }
    </style>

@endsection

@section('javascript')
    <script>
        let map;
        let markers = [];
        let infoWindow;

        // Data from controller
        const leadsData = @json($leads);
        const visitsData = @json($visits);
        const nearbyStoresData = @json($nearbyStores);
        const locationsData = @json($locations);

        function initMap() {
            // Default center (you can change this to your business location)
            const defaultCenter = {
                lat: 37.7749,
                lng: -122.4194
            };

            // Get first business location if available
            let initialCenter = defaultCenter;
            if (locationsData.length > 0 && locationsData[0].latitude && locationsData[0].longitude) {
                initialCenter = {
                    lat: parseFloat(locationsData[0].latitude),
                    lng: parseFloat(locationsData[0].longitude)
                };
            }

            map = new google.maps.Map(document.getElementById('map'), {
                center: initialCenter,
                zoom: 12
            });

            infoWindow = new google.maps.InfoWindow();

            // Add markers
            addLeadMarkers();
            addVisitMarkers();
            addNearbyStoreMarkers();
            addLocationMarkers();
        }

        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }

        function addLeadMarkers() {
            leadsData.forEach(lead => {
                if (lead.latitude && lead.longitude) {
                    const marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(lead.latitude),
                            lng: parseFloat(lead.longitude)
                        },
                        map: map,
                        title: lead.store_name,
                        icon: {
                            url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                        }
                    });

                    marker.addListener('click', function() {
                        const content = `
                        <div>
                            <h4>${lead.store_name || 'N/A'}</h4>
                            <p><strong>Contact:</strong> ${lead.contact_name || 'N/A'}</p>
                            <p><strong>Phone:</strong> ${lead.contact_phone || 'N/A'}</p>
                            <p><strong>Status:</strong> ${lead.lead_status || 'N/A'}</p>
                            <p><strong>City:</strong> ${lead.city || 'N/A'}, ${lead.state || 'N/A'}</p>
                            ${lead.sales_rep ? `<p><strong>Sales Rep:</strong> ${lead.sales_rep.first_name} ${lead.sales_rep.last_name}</p>` : ''}
                            <a href="/leads/${lead.id}" target="_blank" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    `;
                        infoWindow.setContent(content);
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });
        }

        function addVisitMarkers() {
            visitsData.forEach(visit => {
                if (visit.checkin_latitude && visit.checkin_longitude) {
                    const marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(visit.checkin_latitude),
                            lng: parseFloat(visit.checkin_longitude)
                        },
                        map: map,
                        title: visit.lead ? visit.lead.store_name : 'Visit',
                        icon: {
                            url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                        }
                    });

                    marker.addListener('click', function() {
                        const content = `
                        <div>
                            <h4>Visit - ${visit.lead ? visit.lead.store_name : 'Unknown'}</h4>
                            <p><strong>Status:</strong> ${visit.status || 'N/A'}</p>
                            <p><strong>Start Time:</strong> ${visit.start_time || 'N/A'}</p>
                            ${visit.duration ? `<p><strong>Duration:</strong> ${visit.duration} minutes</p>` : ''}
                            ${visit.sales_rep ? `<p><strong>Sales Rep:</strong> ${visit.sales_rep.first_name} ${visit.sales_rep.last_name}</p>` : ''}
                        </div>
                    `;
                        infoWindow.setContent(content);
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });
        }

        function addNearbyStoreMarkers() {
            nearbyStoresData.forEach(store => {
                if (store.latitude && store.longitude) {
                    const marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(store.latitude),
                            lng: parseFloat(store.longitude)
                        },
                        map: map,
                        title: store.store_name,
                        icon: {
                            url: "http://maps.google.com/mapfiles/ms/icons/orange-dot.png"
                        }
                    });

                    marker.addListener('click', function() {
                        const content = `
                        <div>
                            <h4>${store.store_name || 'N/A'}</h4>
                            <p><strong>Address:</strong> ${store.address || 'N/A'}</p>
                            <p><strong>Contact Person:</strong> ${store.contact_person || 'N/A'}</p>
                            <p><strong>Contact Number:</strong> ${store.contact_number || 'N/A'}</p>
                            ${store.discovered_by_sales_rep ? `<p><strong>Discovered By:</strong> ${store.discovered_by_sales_rep.first_name} ${store.discovered_by_sales_rep.last_name}</p>` : ''}
                        </div>
                    `;
                        infoWindow.setContent(content);
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });
        }

        function addLocationMarkers() {
            locationsData.forEach(location => {
                if (location.latitude && location.longitude) {
                    const marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(location.latitude),
                            lng: parseFloat(location.longitude)
                        },
                        map: map,
                        title: location.name,
                        icon: {
                            url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                        }
                    });

                    marker.addListener('click', function() {
                        const content = `
                        <div>
                            <h4>${location.name || 'Business Location'}</h4>
                            <p><strong>Address:</strong> ${location.landmark || 'N/A'}</p>
                            <p><strong>City:</strong> ${location.city || 'N/A'}</p>
                        </div>
                    `;
                        infoWindow.setContent(content);
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });
        }

        $(document).ready(function() {
            // Initialize map
            if (typeof google !== 'undefined') {
                initMap();
            } else {
                console.error('Google Maps API not loaded');
                $('#map').html(
                    '<div class="alert alert-warning">Google Maps API is not configured. Please add your Google Maps API key in the settings.</div>'
                );
            }

            // Filter functionality
            $('#apply_filters').click(function() {
                const salesRepId = $('#filter_sales_rep').val();
                const status = $('#filter_status').val();
                const dateFrom = $('#filter_date_from').val();
                const dateTo = $('#filter_date_to').val();

                const params = new URLSearchParams();
                if (salesRepId) params.append('sales_rep_id', salesRepId);
                if (status) params.append('status', status);
                if (dateFrom) params.append('date_from', dateFrom);
                if (dateTo) params.append('date_to', dateTo);

                window.location.href = '{{ route('maps.index') }}?' + params.toString();
            });

            $('#clear_filters').click(function() {
                window.location.href = '{{ route('maps.index') }}';
            });
        });
    </script>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap&libraries=places"
        async defer></script>
@endsection
