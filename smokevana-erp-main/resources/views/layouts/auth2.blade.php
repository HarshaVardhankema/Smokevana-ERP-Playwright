<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Smokevana ERP') }}</title>

    @include('layouts.partials.css')

    @include('layouts.partials.extracss_auth')

    @if(file_exists(public_path('uploads/business_logos/favicon.ico')))
        <link rel="icon" type="image/x-icon" href="{{ asset('uploads/business_logos/favicon.ico') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="pace-done" data-new-gr-c-s-check-loaded="14.1172.0" data-gr-ext-installed="" cz-shortcut-listen="true">
    @inject('request', 'Illuminate\Http\Request')
    @if (session('status') && session('status.success'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
            data-msg="{{ session('status.msg') }}">
    @endif
    
    <div class="container-fluid" style="max-height: 100vh; overflow-y: auto; padding: 0;">
        @yield('content')
    </div>

    @include('layouts.partials.javascripts')

    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>

    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2_register').select2();
        });
    </script>
    <style>
        .wizard>.content {
            background-color: white !important;
        }
    </style>
</body>

</html>
