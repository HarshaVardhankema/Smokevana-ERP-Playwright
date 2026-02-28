@inject('request', 'Illuminate\Http\Request')

@php
    $pos_layout = ($request->segment(1) == 'pos' && 
        ($request->segment(2) == 'create' || 
         $request->segment(3) == 'edit' || 
         $request->segment(2) == 'payment'));
    
    $whitelist = ['127.0.0.1', '::1'];
@endphp

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
    <title>@yield('title') - {{ Session::get('business.name') }}</title>
    {{-- <link rel="icon" href="/uploads/favicon.ico" type="image/x-icon"> --}}
    @if(file_exists(public_path('uploads/business_logos/favicon.ico')))
        <link rel="icon" type="image/x-icon" href="{{ asset('smokevana-logo.png') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('smokevana-logo.png') }}">
    @endif
    @include('layouts.partials.css')
    @include('layouts.partials.extracss')
    @yield('css')

    @php
        $templateData = session('business.templateData');
        $current_location = null;
        if (auth()->check()) {
            $permitted_locations = auth()->user()->permitted_locations();
            // If user has access to only one location (not multiple), use that location
            if ($permitted_locations != 'all' && is_array($permitted_locations) && count($permitted_locations) == 1) {
                $current_location = \App\BusinessLocation::find($permitted_locations[0]);
            } elseif ($permitted_locations != 'all' && is_array($permitted_locations) && !empty($permitted_locations[0])) {
                // If multiple locations, try to get from session or use first one
                $location_id = session('user.current_location_id', $permitted_locations[0]);
                $current_location = \App\BusinessLocation::find($location_id);
            }
        }
        if ($templateData) {
            // Template variables
            $header_bg = $templateData['header_css']['final_string'];
            $app_bg = $templateData['header_css']['text'];
            $sidebar_bg = $templateData['sidebar_css']['final_string'];
            $sidebar_text = $templateData['sidebar_css']['text'];
            $sidebar_text_active = $templateData['sidebar_text_active'];
            $sidebar_text_hover = $templateData['sidebar_text_hover'];
            $modal_bg = $templateData['modal_css']['final_string'];
            $modal_text = $templateData['modal_css']['text'];
            $tabel_bg = $templateData['tabel_css']['final_string'];
            $tabel_text = $templateData['tabel_css']['text'];
            $logo_css = $templateData['logo_css']['final_string'];
            $homepage_css = $templateData['homepage_css']['final_string'];
            $header_button = $templateData['header_button'];

            // Apply template styles
            if ($header_bg) {
                echo "<style>
                    #header_main_app {
                        background: {$header_bg};
                    }
                </style>";
            }
            if ($app_bg) {
                echo "<style>
                    section {
                        background: {$app_bg} !important;
                    }
                    body > div.tw-flex {
                        background: {$app_bg} !important;
                    }
                    #scrollable-container > div.tw-px-5.tw-py-6 {
                        background: {$app_bg} !important;
                    }
                       .dataTable tbody tr:hover{
                        background: {$app_bg} !important;
                        }
                    #main_background{
                    background: {$app_bg} !important;
                    }  
                </style>";
            }
            if ($sidebar_bg) {
                echo "<style>
                    #side-bar {
                        background: {$sidebar_bg};
                    }
                </style>";
            }
            if ($sidebar_text) {
                echo "<style>
                    .side-bar-normal {
                        color: {$sidebar_text};
                    }
                    #side-bar svg.svg.tw-ml-auto.tw-text-gray-500.tw-size-4.tw-shrink-0 {
                        color: {$sidebar_text};
                    }
                </style>";
            }
            if ($sidebar_text_active) {
                echo "<style>
                    #side-bar .side-bar-active {
                        color: {$sidebar_text_active};
                        background: none;
                    }
                </style>";
            }
            if ($sidebar_text_hover) {
                echo "<style>
                    .side-bar-normal:hover {
                        color: {$sidebar_text_hover};
                    }
                </style>";
            }
            if ($modal_bg) {
                echo "<style>
                    .modal-header {
                        border-radius: 8px;
                        border-bottom-right-radius: 0;
                        border-bottom-left-radius: 0;
                        background: {$modal_bg};
                    }
                    #close_button {
                        background: red;
                        color: white;
                        border: none;
                    }
                </style>";
            }
            if ($modal_text) {
                echo "<style>
                    #modalTitle {
                        color: {$modal_text};
                    }
                    .close {
                        color: {$modal_text};
                        opacity: 1;
                    }
                    .modal-title{
                    color: {$modal_text};
                    }
                    .modal-title small{
                    color: {$modal_text};
                    }
                .modal-title {
                   color: {$modal_text};
                    }
                </style>";
            }
            if ($tabel_bg) {
                echo "<style>
                    .gradiantDiv {
                        background: {$tabel_bg};
                    }
                    thead {
                        background: {$tabel_bg};
                    }
                    .table-bordered > tbody > tr > td,
                    .table-bordered > tbody > tr > th,
                    .table-bordered > tfoot > tr > td,
                    .table-bordered > tfoot > tr > th,
                    .table-bordered > thead > tr > td,
                    .table-bordered > thead > tr > th {
                        border: none;
                    }
                    table.dataTable thead .sorting:after {
                        opacity: 1;
                    }
                </style>";
            }
            if ($tabel_text) {
                echo "<style>
                    thead {
                        color: {$tabel_text};
                    }
                </style>";
            }
            if ($header_button) {
                echo "<style>
                    .tw-dw-btn-primary {
                        background: {$header_button};
                    }
                    .swal-button--confirm {
                        background: {$header_button};
                    }
                    #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > details:nth-child(1) > summary {
                        background: {$header_button};
                    }
                    #btnCalculator {
                        background: {$header_button};
                    }
                    #quick_actions_btn {
                        background: {$header_button};
                    }
                    #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > a {
                        background: {$header_button};
                    }
                    #view_todays_profit {
                        background: {$header_button};
                    }
                    #show_unread_notifications {
                        background: {$header_button};
                    }
                    #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > details:nth-child(7) > summary {
                        background: {$header_button};
                    }
                    #header_main_app > div > div > div > button {
                        background: {$header_button};
                    }
                    #dynamic_button {
                        background: {$header_button};
                    }
                </style>";
            }
            if ($homepage_css) {
                echo "<style>
                    #scrollable-container > div.tw-pb-6 {
                        background: {$homepage_css};
                    }
                    #scrollable-container > div.tw-pb-6 > div.tw-relative > div.tw-px-5.tw-isolate {
                        position: relative;
                        isolation: isolate;
                    }
                    #scrollable-container > div.tw-pb-6 > div.tw-relative > div.tw-px-5.tw-isolate::before {
                        content: \"\";
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 50%;
                        background: {$homepage_css};
                        z-index: -1;
                    }
                </style>";
            }
            if ($logo_css) {
                echo "<style>
                    body > div.tw-flex > aside > a {
                        background: {$logo_css};
                        height: auto;
                    }
                </style>";
            }
        } else if ($current_location && $current_location->is_b2c) {
            echo "<style>
                .modal-title small{
                    color:white;
                    }
                
                 .side-bar-normal:hover {
                        color: rgb(3, 211, 83) !important;
                    }    
                .modal-title {
                    color:white;
                    }
                .dataTable tbody tr:hover{
                    background: rgb(184, 183, 183) !important;
                    }
                .gradiantDiv {
                    background: linear-gradient(272deg, #6BE2F2 -13.09%, #428CD9 11.22%, #4913B7 100%);
                }
                #header_main_app {
                    background: white;
                }
                #scrollable-container > div.tw-pb-6 {
                    background: linear-gradient(272deg, #6BE2F2 -13.09%, #428CD9 11.22%, #4913B7 100%);
                }
                #scrollable-container > div.tw-pb-6 > div.tw-relative > div.tw-px-5.tw-isolate {
                    position: relative;
                    isolation: isolate;
                }
                #scrollable-container > div.tw-pb-6 > div.tw-relative > div.tw-px-5.tw-isolate::before {
                    content: \"\";
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 50%;
                    background: linear-gradient(272deg, #6BE2F2 -13.09%, #428CD9 11.22%, #4913B7 100%);
                    z-index: -1;
                }
                .close {
                    color: white;
                    opacity: 1;
                }
                #close_button {
                    background: red;
                    color: white;
                    border: none;
                }
                #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > details:nth-child(2) > summary {
                    background: #380989;
                }
                #btnCalculator {
                    background: #380989;
                }
                #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > a {
                    background: #380989;
                }
                #view_todays_profit {
                    background: #380989;
                }
                #show_unread_notifications {
                    background: #380989;
                }
                .tw-dw-btn-primary {
                    background: #4822BB;
                }
                #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > details:nth-child(8) > summary {
                    background: #380989;
                }
                #header_main_app > div > div > div > button {
                    background: #380989;
                }
                .swal-button--confirm {
                    background: #380989;
                }
                #header_main_app {
                    color: black;
                }
                body > div.tw-flex > aside > a {
                    background: white;
                }
                #side-bar {
                    background: linear-gradient(0.07deg, #0E0E44 -31.63%, #4649C7 99.95%);
                }
                .side-bar-normal {
                    color: white;
                }
                #side-bar svg.svg.tw-ml-auto.tw-text-gray-500.tw-size-4.tw-shrink-0 {
                    color: white;
                }
                #side-bar .side-bar-active {
                    color: #6dbfb8;
                    background: none;
                }
                thead {
                    background: linear-gradient(272deg, #6BE2F2 -13.09%, #428CD9 11.22%, #4913B7 100%);
                    color: white;
                }
                .table-bordered > tbody > tr > td,
                .table-bordered > tbody > tr > th,
                .table-bordered > tfoot > tr > td,
                .table-bordered > tfoot > tr > th,
                .table-bordered > thead > tr > td,
                .table-bordered > thead > tr > th {
                    border: none;
                }
                table.dataTable thead .sorting:after {
                    opacity: 1;
                }
                .modal-header {
                    border-radius: 8px;
                    border-bottom-right-radius: 0;
                    border-bottom-left-radius: 0;
                    background: linear-gradient(272deg, #6BE2F2 -13.09%, #428CD9 11.22%, #4913B7 100%);
                }
                body > div.tw-flex > aside > a {
                    height: auto;
                }
            </style>";
            } else {
                echo "<style>
                .modal-title small{
                    color:white;
                    }
                
                 .side-bar-normal:hover {
                        color: rgb(247, 190, 3) !important;
                    }    
                .modal-title {
                    color:white;
                    }
                .dataTable tbody tr:hover{
                    background: rgb(184, 183, 183) !important;
                    }
                .gradiantDiv {
                    background: linear-gradient(272deg, #8B3B08 -13.09%, #DB8700 11.22%, #8B3B08 100%);
                }
                #header_main_app {
                    background: #FFFFFF;
                }
                #scrollable-container > div.tw-pb-6 {
                    background: linear-gradient(272deg, #8B3B08 -13.09%, #DB8700 11.22%, #8B3B08 100%);
                }
                #scrollable-container > div.tw-pb-6 > div.tw-relative > div.tw-px-5.tw-isolate {
                    position: relative;
                    isolation: isolate;
                }
                #scrollable-container > div.tw-pb-6 > div.tw-relative > div.tw-px-5.tw-isolate::before {
                    content: \"\";
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 50%;
                    background: linear-gradient(272deg, #8B3B08 -13.09%, #DB8700 11.22%, #8B3B08 100%);
                    z-index: -1;
                }
                .close {
                    color: white;
                    opacity: 1;
                }
                #close_button {
                    background: red;
                    color: white;
                    border: none;
                }
                #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > details:nth-child(1) > summary {
                    background: #8B3B08;
                }
                #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > details:nth-child(2) > summary {
                    background: #8B3B08;
                }
                #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > details:nth-child(7) > summary{
                    background: #8B3B08;
                }
                #btnCalculator {
                    background: #8B3B08;
                }
                #quick_actions_btn {
                    background: #8B3B08;
                }
                #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > a {
                    background: #8B3B08;
                }
                #view_todays_profit {
                    background: #8B3B08;
                }
                #show_unread_notifications {
                    background: #8B3B08;
                }
                .tw-dw-btn-primary {
                    background: #DB8700;
                }
                #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > details:nth-child(8) > summary {
                    background: #8B3B08;
                }
                #header_main_app > div > div > div > button {
                    background: #8B3B08;
                }
                .swal-button--confirm {
                    background: #8B3B08;
                }
                #header_main_app {
                    color: #75411A;
                }
                body > div.tw-flex > aside > a {
                    background: #FFFFFF;
                }
                #side-bar {
                    background: linear-gradient(180deg, #BE7024 -31.63%, #3F1600 99.95%);
                }
                .side-bar-normal {
                    color: white;
                }
                #side-bar svg.svg.tw-ml-auto.tw-text-gray-500.tw-size-4.tw-shrink-0 {
                    color: white;
                }
                #side-bar .side-bar-active {
                    color: #F3D58D;
                    background: none;
                }
                thead {
                    background: linear-gradient(272deg, #8B3B08 -13.09%, #DB8700 11.22%, #8B3B08 100%);
                    color: white;
                }
                .table-bordered > tbody > tr > td,
                .table-bordered > tbody > tr > th,
                .table-bordered > tfoot > tr > td,
                .table-bordered > tfoot > tr > th,
                .table-bordered > thead > tr > td,
                .table-bordered > thead > tr > th {
                    border: none;
                }
                table.dataTable thead .sorting:after {
                    opacity: 1;
                }
                .modal-header {
                    border-radius: 8px;
                    border-bottom-right-radius: 0;
                    border-bottom-left-radius: 0;
                    background: linear-gradient(272deg, #8B3B08 -13.09%, #DB8700 11.22%, #8B3B08 100%);
                }
                .modal-content {
                    background: #FFF7EC;
                }
                #main_background {
                    background: #FFF7EC;
                }
                body {
                    background-color: #FFF7EC;
                }
                body > div.tw-flex > aside > a {
                    height: auto;
                }
                    section {
                        background: #FFF7EC !important;
                    }
                #scrollable-container > p{
                background: #FFF7EC !important;
                }        
            </style>";
           
        }
    @endphp

    <style>
        .tw-dw-btn:hover{
            color: white !important;
        }
        .dataTables_scrollHeadInner {
            width: 100% !important;
        }
        table {
            width: 100% !important;
        }
        ul.dt-button-collection {
            background: white;
        }
        .dropdown-menu > .active > a {
            color: #fff;
            text-decoration: none;
            background-color: #337ab7;
            outline: 0;
            border: 2px solid blue;
            border-radius: 5%;
            margin: 2%;
        }
        .loader {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            display: inline-block;
            border-top: 4px solid #41f741;
            border-right: 4px solid transparent;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }
        .loader::after {
            content: '';
            box-sizing: border-box;
            position: absolute;
            left: 0;
            top: 0;
            width: 75px;
            height: 75px;
            border-radius: 50%;
            border-left: 4px solid #6d4dff;
            border-bottom: 4px solid transparent;
            animation: rotation 0.5s linear infinite reverse;
        }
        div.dataTables_wrapper {
    position: relative; /* Ensure the wrapper is the positioning context */
}

div.dataTables_wrapper div.dataTables_processing {
    position: absolute; /* Position relative to wrapper */
    z-index: 2;
    background: rgba(255, 255, 255, 0.5); /* Corrected RGBA value */
    top: 0 !important;
    left: 0 !important;
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 !important;
    border: none !important;
}
        .dropdown-menu {
            z-index: 9999999 !important;
        }
        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        thead {
            white-space: nowrap;
        }
        .col-md-1-5 {
            width: 12.5%;
            float: left;
            padding: 0 15px; 
        }
        #header_main_app > div > div > div.tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-3 > details:nth-child(7) > ul{
            z-index:9999;
        }
    </style>
</head>

<body class="tw-font-sans tw-antialiased tw-text-gray-900 tw-bg-gray-100 @if($pos_layout) hold-transition lockscreen @else hold-transition skin-@if(!empty(session('business.theme_color'))){{ session('business.theme_color') }}@else{{ 'blue-light' }}@endif sidebar-mini @endif">
    <div id="main_loader" class="hidden" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);display:flex;align-items:center;justify-content:center;width:100vw;height:100vh;background:rgba(255,255,255,30%);z-index:9999;">
        <span class="loader"></span>
    </div>
    
    <div class="app-page-wrapper tw-flex tw-flex-col tw-min-h-screen">
    <div class="tw-flex tw-flex-1 tw-min-h-0 tw-w-full" id="main_background">
        <script type="text/javascript">
            if(localStorage.getItem("upos_sidebar_collapse") == 'true'){
                var body = document.getElementsByTagName("body")[0];
                body.className += " sidebar-collapse";
            }
        </script>
        
        @if(!$pos_layout)
            @include('layouts.partials.sidebar')
        @endif

        @if(in_array($_SERVER['REMOTE_ADDR'], $whitelist))
            <input type="hidden" id="__is_localhost" value="true">
        @endif

        <!-- Currency related fields -->
        <input type="hidden" id="__code" value="{{ session('currency')['code'] }}">
        <input type="hidden" id="__symbol" value="{{ session('currency')['symbol'] }}">
        <input type="hidden" id="__thousand" value="{{ session('currency')['thousand_separator'] }}">
        <input type="hidden" id="__decimal" value="{{ session('currency')['decimal_separator'] }}">
        <input type="hidden" id="__symbol_placement" value="{{ session('business.currency_symbol_placement') }}">
        <input type="hidden" id="__precision" value="{{ session('business.currency_precision', 2) }}">
        <input type="hidden" id="__quantity_precision" value="{{ session('business.quantity_precision', 2) }}">

        @can('view_export_buttons')
            <input type="hidden" id="view_export_buttons">
        @endcan

        @if(isMobile())
            <input type="hidden" id="__is_mobile">
        @endif

        @if(session('status'))
            <input type="hidden" id="status_span" 
                   data-status="{{ session('status.success') }}"
                   data-msg="{{ session('status.msg') }}">
        @endif

        <main class="tw-flex tw-flex-col tw-flex-1 tw-h-full tw-min-w-0 tw-bg-gray-100">
            @if(!$pos_layout)
                @include('layouts.partials.header')
            @else
                @include('layouts.partials.header-pos')
            @endif

            <!-- Vue.js container -->
            <div id="app">
                @yield('vue')
            </div>

            <div class="tw-flex-1 tw-overflow-y-auto tw-h-screen" id="scrollable-container">
                @yield('content')
                @if($pos_layout)
                    @include('layouts.partials.footer_pos')
                @endif
            </div>

            <div class='scrolltop no-print'>
                <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
            </div>

            @if(config('constants.iraqi_selling_price_adjustment'))
                <input type="hidden" id="iraqi_selling_price_adjustment">
            @endif

            <!-- Print section -->
            <section class="invoice print_section" id="receipt_section"></section>
        </main>

        @include('home.todays_profit_modal')

        @if(!$pos_layout)
            @include('layouts.partials.quick_actions_panel')
        @endif

        <!-- Audio elements -->
        <audio id="success-audio">
            <source src="{{ asset('/audio/success.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/success.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="error-audio">
            <source src="{{ asset('/audio/error.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/error.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="warning-audio">
            <source src="{{ asset('/audio/warning.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/warning.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>

        @if(!empty($__additional_html))
            {!! $__additional_html !!}
        @endif

        @include('layouts.partials.javascripts')

        <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static"></div>

        @if(!empty($__additional_views) && is_array($__additional_views))
            @foreach($__additional_views as $additional_view)
                @includeIf($additional_view)
            @endforeach
        @endif

        <div>
            <div class="overlay tw-hidden"></div>
        </div>
    </div>
    @if(!$pos_layout)
        @include('layouts.partials.footer')
    @endif
    </div>
</body>

<style>
    @media print {
        #scrollable-container {
            overflow: visible !important;
            height: auto !important;
        }

        /* When printing an invoice receipt, hide everything except #receipt_section */
        body.printing-receipt * {
            visibility: hidden !important;
        }

        body.printing-receipt #receipt_section,
        body.printing-receipt #receipt_section * {
            visibility: visible !important;
        }

        body.printing-receipt #receipt_section {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body.printing-receipt .modal,
        body.printing-receipt .modal-backdrop {
            display: none !important;
        }
    }

    .small-view-side-active {
        display: grid !important;
        z-index: 1000;
        position: absolute;
    }

    .overlay {
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.8);
        position: fixed;
        top: 0;
        left: 0;
        display: none;
        z-index: 20;
    }

    .tw-dw-btn.tw-dw-btn-xs.tw-dw-btn-outline {
        width: max-content;
        margin: 2px;
    }

    #scrollable-container {
        position: relative;
    }
    td{
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        align-content: center !important;
    }
    .box-header{
        padding-bottom: 0px;
        padding-top:0; 


    }
    .content {
        padding-top:0 !important;
    }
    ::-webkit-scrollbar-track {
                            background-color: #e9feff;
                            border-radius: 5px;
                            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
                          }
                          
                          /* Scrollbar (applies to both vertical and horizontal) */
                          ::-webkit-scrollbar {
                            background-color: #bbd7d8;
                            width: 10px;
                            height: 10px; /* Add this for horizontal scrollbar */
                          }
                          
                          /* Scrollbar thumb (draggable part) */
                          ::-webkit-scrollbar-thumb {
                            background-color: #beecee;
                            border-radius: 5px;
                            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
                          }
                          .nav>li>a {
                              padding: 5px 7px !important;
                          }
                          .box{
                              margin-bottom: 8px;
                          }
.content{
    margin-top: 0% !important;
    margin-bottom:0% !important;
    padding-bottom: 0 !important;
}
section{
    padding-top: 3px !important;
}
div.bottom{
display: flex;
justify-content:space-between
}
.form-control{
    border-radius:5px;
}
 .input-group-addon{
    background: rgb(236, 236, 236) !important;
     border-top-left-radius: 5px !important;
      border-bottom-left-radius: 5px !important;
}
.ui-menu{
    z-index: 9999 !important;
}
.btn-danger{
    background: #ff0033 !important;
    border-color: #ff0033 !important;
    border-radius: 15% !important;
}
.scroll-safe-dropdown__menu{
    z-index: 2050;
}
.scroll-safe-dropdown__menu--dropup{
    transform-origin: bottom center;
}

</style>
</html>
