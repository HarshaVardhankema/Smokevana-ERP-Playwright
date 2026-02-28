@extends('layouts.app')

@section('title', __('My Map View'))

@section('content')
    <section class="content-header">
        <h1>@lang('My Map - Leads & Visits')</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('My Leads and Visits')</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Legend -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="map-legend">
                                    <strong>@lang('Legend:')</strong>
                                    <span class="legend-item"><i class="fa fa-map-marker text-blue"></i>
                                        @lang('My Leads')</span>
                                    <span class="legend-item"><i class="fa fa-map-marker text-green"></i>
                                        @lang('My Visits')</span>
                                    <span class="legend-item"><i class="fa fa-map-marker text-purple"></i>
                                        @lang('My Current Location')</span>
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

                        <hr>

                        <!-- Quick Actions -->
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-primary" id="show_my_location">
                                    <i class="fa fa-crosshairs"></i> @lang('Show My Location')
                                </button>
                                <button class="btn btn-success" id="find_nearby_leads">
                                    <i class="fa fa-search"></i> @lang('Find Nearby Leads')
                                </button>
                                <a href="{{ route('leads.create') }}" class="btn btn-info">
                                    <i class="fa fa-plus"></i> @lang('Add New Lead')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-map-marker"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('My Leads')</span>
                        <span class="info-box-number" id="my_leads_count">{{ $myLeads->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('My Visits')</span>
                        <span class="info-box-number" id="my_visits_count">{{ $myVisits->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('Today\'s Visits')</span>
                        <span
                            class="info-box-number">{{ $myVisits->where('start_time', '>=', now()->startOfDay())->count() }}</span>
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
        let userLocationMarker;

        // Data from controller
        const myLeadsData = @json($myLeads);
        const myVisitsData = @json($myVisits);
        const currentLocation = @json($currentLocation);

        function initMap() {
            // Default center
            const defaultCenter = {
                lat: 37.7749,
                lng: -122.4194
            };

            // Use current location if available
            let initialCenter = defaultCenter;
            if (currentLocation && currentLocation.latitude && currentLocation.longitude) {
                initialCenter = {
                    lat: parseFloat(currentLocation.latitude),
                    lng: parseFloat(currentLocation.longitude)
                };
            } else if (myLeadsData.length > 0 && myLeadsData[0].latitude && myLeadsData[0].longitude) {
                initialCenter = {
                    lat: parseFloat(myLeadsData[0].latitude),
                    lng: parseFloat(myLeadsData[0].longitude)
                };
            }

            map = new google.maps.Map(document.getElementById('map'), {
                center: initialCenter,
                zoom: 12
            });

            infoWindow = new google.maps.InfoWindow();

            // Add markers
            addMyLeadMarkers();
            addMyVisitMarkers();

            if (currentLocation) {
                showUserLocation(currentLocation.latitude, currentLocation.longitude);
            }
        }

        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }

        function addMyLeadMarkers() {
            myLeadsData.forEach(lead => {
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
                            <h4>${lead.store_name || lead.business_name || 'N/A'}</h4>
                            <p><strong>Contact:</strong> ${lead.contact_name || 'N/A'}</p>
                            <p><strong>Phone:</strong> ${lead.contact_phone || 'N/A'}</p>
                            <p><strong>Status:</strong> ${lead.lead_status || 'N/A'}</p>
                            <p><strong>Address:</strong> ${lead.address_line_1 || 'N/A'}</p>
                            <p><strong>City:</strong> ${lead.city || 'N/A'}, ${lead.state || 'N/A'}</p>
                            <a href="/leads/${lead.id}" target="_blank" class="btn btn-sm btn-primary">View Details</a>
                            <button class="btn btn-sm btn-success" onclick="startVisit(${lead.id})">Start Visit</button>
                        </div>
                    `;
                        infoWindow.setContent(content);
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });
        }

        function addMyVisitMarkers() {
            myVisitsData.forEach(visit => {
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
                            <a href="/visit-tracking" target="_blank" class="btn btn-sm btn-primary">View All Visits</a>
                        </div>
                    `;
                        infoWindow.setContent(content);
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });
        }

        function showUserLocation(lat, lng) {
            if (userLocationMarker) {
                userLocationMarker.setMap(null);
            }

            userLocationMarker = new google.maps.Marker({
                position: {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                },
                map: map,
                title: 'My Current Location',
                icon: {
                    url: "http://maps.google.com/mapfiles/ms/icons/purple-dot.png"
                }
            });

            map.setCenter({
                lat: parseFloat(lat),
                lng: parseFloat(lng)
            });
            map.setZoom(14);
        }

        function startVisit(leadId) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const notes = prompt('Visit notes (optional):');

                    $.ajax({
                        url: '/maps/api/create-visit',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            lead_id: leadId,
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            notes: notes
                        },
                        success: function(response) {
                            toastr.success('Visit started successfully!');
                            setTimeout(() => window.location.reload(), 1500);
                        },
                        error: function(xhr) {
                            toastr.error('Error starting visit: ' + (xhr.responseJSON?.message ||
                                'Unknown error'));
                        }
                    });
                }, function(error) {
                    toastr.error('Unable to get your location. Please enable location services.');
                });
            } else {
                toastr.error('Geolocation is not supported by your browser.');
            }
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

            // Show my location button
            $('#show_my_location').click(function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        showUserLocation(position.coords.latitude, position.coords.longitude);
                        toastr.success('Location updated!');
                    }, function(error) {
                        toastr.error(
                            'Unable to get your location. Please enable location services.');
                    });
                } else {
                    toastr.error('Geolocation is not supported by your browser.');
                }
            });

            // Find nearby leads
            $('#find_nearby_leads').click(function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        $.ajax({
                            url: '/maps/api/nearby-leads',
                            type: 'GET',
                            data: {
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                                radius: 5
                            },
                            success: function(response) {
                                if (response.success && response.data.leads.length >
                                    0) {
                                    toastr.success(
                                        `Found ${response.data.leads.length} nearby leads!`
                                    );
                                    // Optionally refresh markers with nearby leads
                                } else {
                                    toastr.info(
                                        'No nearby leads found within 5km radius.');
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Error finding nearby leads');
                            }
                        });
                    }, function(error) {
                        toastr.error(
                            'Unable to get your location. Please enable location services.');
                    });
                } else {
                    toastr.error('Geolocation is not supported by your browser.');
                }
            });
        });
    </script>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap&libraries=places"
        async defer></script>
@endsection
