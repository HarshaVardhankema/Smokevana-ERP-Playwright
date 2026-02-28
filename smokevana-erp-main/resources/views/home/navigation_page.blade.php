<!DOCTYPE html>
<html class="tw-bg-white tw-scroll-smooth" 
      lang="{{ app()->getLocale() }}"
      dir="{{ in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" 
          content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"
          name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Store Selection - {{ Session::get('business.name') }}</title>
    @if(file_exists(public_path('uploads/business_logos/favicon.ico')))
        <link rel="icon" type="image/x-icon" href="{{ asset('uploads/business_logos/favicon.ico/favicon.ico') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    @include('layouts.partials.css')
    @include('layouts.partials.extracss')
</head>
<body class="tw-font-sans tw-antialiased tw-text-gray-900 tw-bg-gray-100">
    <div id="main_loader" class="hidden" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);display:flex;align-items:center;justify-content:center;width:100vw;height:100vh;background:rgba(255,255,255,30%);z-index:9999;">
        <span class="loader"></span>
    </div>
    <main class="tw-flex tw-flex-col tw-flex-1 tw-h-full tw-min-w-0" style="width: 100%; background: #EAEDED; min-height: 100vh;">
        <section class="content-header" style="padding: 3rem 2rem 2rem; text-align: center; background: transparent;">
                <h1 class="tw-text-3xl md:tw-text-5xl tw-font-bold" style="margin-bottom: 0.5rem; color: #0F1111;">Store Selection</h1>
                <p class="tw-text-base md:tw-text-lg tw-font-medium" style="color: #0F1111;">Where would you like to work today?</p>
        </section>

        <section class="content" style="padding: 2rem; display: flex; justify-content: center; align-items: center; width: 100%;">
            <div class="container-fluid modern-container" style="width: 100%; max-width: 100%; margin: 0 auto; display: flex; justify-content: center; align-items: center;">
                <!-- Locations Grid -->
                <div id="locationsGrid">
                @forelse($business_locations as $location)
                    <div class="location-item {{ !$location->is_b2c ? 'b2c-card' : 'b2b-card' }}" data-location-id="{{ $location->id }}"
                        data-location-name="{{ $location->name }}"
                        data-status="{{ $location->is_active ? 'active' : 'inactive' }}"
                        data-type="{{ !$location->is_b2c ? 'b2c' : 'b2b' }}">
                        <div class="modern-location-card">
                            <div class="card-header gradiantDiv {{ !$location->is_b2c ? 'b2c-header' : 'b2b-header' }}">
                                <div class="location-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="card-header-title" style="color: white; font-size:1rem; font-weight: 700;">
                                    {{ $location->is_b2c ? 'Retail' : 'Wholesale'}}
                                </div>
                                <div class="status-badge">
                                    @if($location->is_active)
                                        <span class="badge badge-success pulse">
                                            <i class="fas fa-circle"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-pause-circle"></i> Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body {{ !$location->is_b2c ? 'b2c-body' : 'b2b-body' }}">
                                <h5 class="location-name {{ !$location->is_b2c ? 'b2c-text' : '' }}">{{ $location->name }}</h5>
                                <p class="location-id {{ !$location->is_b2c ? 'b2c-text' : '' }}">ID: {{ $location->location_id }}</p>
                                <div class="location-details">
                                    <div class="detail-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $location->city }}, {{ $location->state }}</span>
                                    </div>

                                    @if($location->landmark)
                                        <div class="detail-item">
                                            <i class="fas fa-landmark"></i>
                                            <span>{{ $location->landmark }}</span>
                                        </div>
                                    @endif

                                    @if($location->mobile)
                                        <div class="detail-item">
                                            <i class="fas fa-phone"></i>
                                            <span>{{ $location->mobile }}</span>
                                        </div>
                                    @endif

                                    @if($location->email)
                                        <div class="detail-item">
                                            <i class="fas fa-envelope"></i>
                                            <span>{{ $location->email }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-footer {{ !$location->is_b2c ? 'b2c-footer' : 'b2b-footer' }}">
                                <div class="location-type">
                                    @if($location->is_b2c)
                                        <span class="badge badge-primary">
                                            <i class="fas fa-users"></i> B2C
                                        </span>
                                    @else
                                        <span class="badge badge-b2b">
                                            <i class="fas fa-building"></i> B2B
                                        </span>
                                    @endif
                                </div>
                                <div class="card-actions">
                                    <button class="tw-dw-btn {{ !$location->is_b2c ? 'tw-dw-btn-b2c' : 'tw-dw-btn-primary' }} text-white select-location"
                                        data-location-id="{{ $location->id }}">
                                        <i class="fas fa-arrow-right"></i> Select
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="location-item" style="grid-column: 1 / -1;">
                        <div class="modern-empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <h4>No Business Locations Found</h4>
                            <p>Please contact your administrator to set up business locations.</p>
                            <button class="btn btn-modern btn-primary" onclick="location.reload()">
                                <i class="fas fa-refresh"></i> Refresh Page
                            </button>
                        </div>
                    </div>
                @endforelse                
            </div>
        </div>
    </section>
    </main>

    <!-- Essential CSS Styles -->
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .modern-container {
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .modern-location-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 4px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            overflow: hidden;
            height: 380px;
            width: 380px;
            max-width: 380px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            position: relative;
            margin: 0 auto;
        }

        .modern-location-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #FF9900 0%, #E47911 100%);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .modern-location-card:hover::before {
            transform: scaleX(1);
        }

        .modern-location-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(255, 153, 0, 0.2), 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* Amazon theme: both cards – dark header + orange (second color) accent */
        .card-header {
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            min-height: 70px;
            height: 70px;
            flex-shrink: 0;
            background: linear-gradient(to bottom, #232f3e 0%, #37475a 100%);
            border-bottom: 3px solid #FF9900;
            position: relative;
            overflow: hidden;
        }

        .b2c-header,
        .b2b-header {
            background: linear-gradient(to bottom, #232f3e 0%, #37475a 100%) !important;
            border-bottom: 3px solid #FF9900;
        }
        
        .card-header-title {
            flex: 1;
            text-align: center;
            margin: 0 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        
        .location-icon {
            flex-shrink: 0;
        }
        
        .status-badge {
            flex-shrink: 0;
        }

        .card-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transition: transform 0.6s ease;
        }

        .modern-location-card:hover .card-header::after {
            transform: rotate(45deg);
        }

        .location-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            z-index: 1;
        }

        .modern-location-card:hover .location-icon {
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.1) rotate(5deg);
        }

        .status-badge {
            z-index: 1;
        }

        .status-badge .badge {
            padding: 0.4rem 0.9rem;
            border-radius: 18px;
            font-weight: 600;
            font-size: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .badge-secondary {
            background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
            color: white;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        .card-body {
            padding: 1rem 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            background: #f7f8f8;
            min-height: 0;
        }

        .b2c-body,
        .b2b-body {
            background: #f7f8f8 !important;
        }
        
        .card-body::-webkit-scrollbar {
            width: 6px;
        }
        
        .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .card-body::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }
        
        .card-body::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .location-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a202c;
            margin: 0 0 0.5rem 0;
            line-height: 1.3;
        }

        .b2c-text {
            color: #0F1111 !important;
        }

        .location-id {
            color: #718096;
            font-size: 0.8rem;
            margin: 0 0 0.5rem 0;
            font-weight: 500;
            background: #f7fafc;
            padding: 0.3rem 0.7rem;
            border-radius: 8px;
            display: inline-block;
            width: fit-content;
        }

        .location-address,
        .location-city,
        .location-state {
            color: #718096;
            font-size: 0.8rem;
            margin: 0.25rem 0;
            font-weight: 400;
            line-height: 1.4;
        }

        .b2c-text.location-id {
            background: #FFF3E0;
            color: #0F1111;
        }

        .b2c-text.location-address,
        .b2c-text.location-city,
        .b2c-text.location-state {
            color: #0F1111 !important;
        }

        .location-details {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #4a5568;
            font-size: 0.9rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .detail-item:hover {
            background: #f7fafc;
            transform: translateX(5px);
        }

        .detail-item i {
            width: 20px;
            height: 20px;
            color: #FF9900;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 153, 0, 0.12);
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .card-footer {
            background: linear-gradient(to bottom, #F7F8F8 0%, #E7E9EC 100%);
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #FF9900;
            min-height: 70px;
            height: 70px;
            flex-shrink: 0;
            margin-top: auto;
        }

        .b2c-footer,
        .b2b-footer {
            background: linear-gradient(to bottom, #F7F8F8 0%, #E7E9EC 100%) !important;
            border-top: 2px solid #FF9900;
        }

        .location-type .badge {
            padding: 0.5rem 1rem;
            border-radius: 18px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-info {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
        }

        .badge-primary {
            background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
            color: white;
        }

        .badge-b2b {
            background: linear-gradient(to bottom, #232f3e 0%, #37475a 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 18px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-modern {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-modern.btn-primary {
            background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
            color: white;
        }

        .btn-modern.btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 153, 0, 0.3);
        }

        .tw-dw-btn {
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
        }

        /* Amazon theme second color (orange) – both cards Select button */
        .tw-dw-btn-primary,
        .tw-dw-btn-b2c {
            background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 153, 0, 0.3);
        }

        .tw-dw-btn-primary:hover,
        .tw-dw-btn-b2c:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 153, 0, 0.4);
        }

        .tw-dw-btn-primary:active {
            transform: translateY(0);
        }

        .modern-empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 2rem 0;
        }

        .empty-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 3rem;
            background: linear-gradient(to bottom, #232f3e 0%, #37475a 100%);
            box-shadow: 0 10px 30px rgba(255, 153, 0, 0.25);
        }

        .location-item.selected .modern-location-card {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 30px 60px rgba(255, 153, 0, 0.25), 0 10px 20px rgba(0, 0, 0, 0.2);
            border: 2px solid #FF9900;
        }

        .location-item.selected .card-header {
            background: linear-gradient(to bottom, #232f3e 0%, #37475a 100%);
        }

        .location-item.hidden {
            display: none;
        }

        .card-actions {
            display: flex;
            align-items: center;
        }

        .location-item {
            width: 100%;
            max-width: 100%;
            display: flex;
            align-items: stretch;
            height: 100%;
            justify-content: center;
        }

        .location-item .modern-location-card {
            width: 380px;
            max-width: 380px;
            height: 380px;
            display: flex;
            flex-direction: column;
            margin: 0 auto;
        }

        /* Small Mobile adjustments */
        @media (max-width: 576px) {
            #locationsGrid {
                grid-template-columns: 1fr !important;
                gap: 1.5rem;
                padding: 0 0.5rem;
            }
            
            .content-header {
                padding: 2rem 1rem 1.5rem !important;
            }
            
            .content-header h1 {
                font-size: 2rem !important;
            }
            
            .content {
                padding: 1rem !important;
            }
            
            .modern-location-card,
            .location-item .modern-location-card {
                height: 340px;
                width: 340px;
                max-width: 340px;
            }
        }

        /* Grid layout - Dynamic and Responsive */
        #locationsGrid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 380px));
            gap: 2rem;
            padding: 0;
            align-items: center;
            justify-items: center;
            justify-content: center;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            place-items: center;
        }

        /* Mobile - Single column */
        @media (max-width: 767px) {
            #locationsGrid {
                grid-template-columns: 1fr !important;
                gap: 2rem;
                padding: 0 1rem;
            }
            
            .modern-location-card,
            .location-item .modern-location-card {
                height: 360px;
                width: 360px;
                max-width: 360px;
            }
        }

        /* Tablet - 2 columns */
        @media (min-width: 768px) and (max-width: 1199px) {
            #locationsGrid {
                grid-template-columns: repeat(auto-fit, minmax(380px, 380px));
                gap: 2rem;
                padding: 0 2rem;
                justify-content: center;
            }
        }

        /* Desktop - 2 columns with wider cards */
        @media (min-width: 1200px) and (max-width: 1599px) {
            #locationsGrid {
                grid-template-columns: repeat(auto-fit, minmax(380px, 380px));
                gap: 2.5rem;
                padding: 0 3rem;
                justify-content: center;
            }
        }

        /* Large Desktop - 3 columns */
        @media (min-width: 1600px) {
            #locationsGrid {
                grid-template-columns: repeat(auto-fit, minmax(380px, 380px));
                gap: 2.5rem;
                padding: 0 4rem;
                justify-content: center;
            }
        }

        /* Extra Large - 3 columns with more spacing */
        @media (min-width: 1920px) {
            #locationsGrid {
                grid-template-columns: repeat(auto-fit, minmax(380px, 380px));
                gap: 3rem;
                padding: 0 5rem;
                justify-content: center;
            }
        }

        /* Smooth animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .location-item {
            animation: fadeInUp 0.6s ease-out;
        }

        .location-item:nth-child(1) { animation-delay: 0.1s; }
        .location-item:nth-child(2) { animation-delay: 0.2s; }
        .location-item:nth-child(3) { animation-delay: 0.3s; }
        .location-item:nth-child(4) { animation-delay: 0.4s; }
        .location-item:nth-child(5) { animation-delay: 0.5s; }
        .location-item:nth-child(6) { animation-delay: 0.6s; }
    </style>

    @include('layouts.partials.javascripts')
    <script type="text/javascript">
        $(document).ready(function () {
            // Location selection
            $(document).on('click', '.select-location', function (e) {
                e.stopPropagation();
                const locationId = $(this).data('location-id');
                const locationName = $(this).closest('.location-item').data('location-name');
                selectLocation(locationId, locationName);
            });

            $(document).on('click', '.select-navigation', function (e) {
                e.stopPropagation();
                const url= $(this).data('url');
                window.location.href = url;
            });

            // Card click selection
            $(document).on('click', '.modern-location-card', function (e) {
                if (!$(e.target).closest('.select-location').length) {
                    const locationId = $(this).closest('.location-item').data('location-id');
                    const locationName = $(this).closest('.location-item').data('location-name');
                    selectLocation(locationId, locationName);
                }
            });

            function selectLocation(locationId, locationName) {
                // Remove previous selection
                $('.location-item').removeClass('selected');

                // Add selection to current item
                $(`.location-item[data-location-id="${locationId}"]`).addClass('selected');

                // Show loading message
                toastr.info(`Selecting "${locationName}"...`);

                // Make AJAX call to update location
                $.ajax({
                    url: `/users/update-location`,
                    type: 'POST',
                    data: {
                        location_permissions: [`location.${locationId}`],
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.msg);

                            // Redirect after a short delay
                            setTimeout(() => {
                                window.location.href = `/home`;
                            }, 1500);
                        } else {
                            toastr.error(response.msg || 'Failed to update location');
                        }
                    },
                    error: function (xhr, status, error) {
                        let errorMsg = 'Failed to update location';

                        if (xhr.responseJSON && xhr.responseJSON.msg) {
                            errorMsg = xhr.responseJSON.msg;
                        } else if (xhr.status === 422) {
                            errorMsg = 'Invalid location selected';
                        } else if (xhr.status === 403) {
                            errorMsg = 'Access denied to this location';
                        } else if (xhr.status === 404) {
                            errorMsg = 'User not found';
                        }

                        toastr.error(errorMsg);

                        // Remove selection on error
                        $('.location-item').removeClass('selected');
                    }
                });
            }

            // Add interactive effects
            $('.modern-location-card').hover(
                function () {
                    $(this).find('.location-icon').addClass('pulse');
                },
                function () {
                    $(this).find('.location-icon').removeClass('pulse');
                }
            );
        });
    </script>
</body>
</html>