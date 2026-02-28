@extends('layouts.auth2')
@section('title', __('lang_v1.login'))
@inject('request', 'Illuminate\Http\Request')
@section('content')
    @php
        $username = old('username');
        $password = null;
        if (config('app.env') == 'demo') {
            $username = 'admin';
            $password = '123456';

            $demo_types = [
                'all_in_one' => 'admin',
                'super_market' => 'admin',
                'pharmacy' => 'admin-pharmacy',
                'electronics' => 'admin-electronics',
                'services' => 'admin-services',
                'restaurant' => 'admin-restaurant',
                'superadmin' => 'superadmin',
                'woocommerce' => 'woocommerce_user',
                'essentials' => 'admin-essentials',
                'manufacturing' => 'manufacturer-demo',
            ];

            if (!empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types)) {
                $username = $demo_types[$_GET['demo_type']];
            }
        }
    @endphp
    <style>
        /* Amazon-style Complete Theme */
        * {
            box-sizing: border-box;
        }

        .amazon-login-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px 20px;
        }

        /* Logo Card Section */
        .amazon-logo-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #e3e6e8;
            border-radius: 8px;
            padding: 30px 50px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.04);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .amazon-logo-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff9900, #ffad33, #ff9900);
        }

        .amazon-logo-card img {
            max-width: 280px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Description Section */
        .amazon-description {
            text-align: center;
            margin-bottom: 25px;
            max-width: 450px;
        }

        .amazon-description .tagline {
            font-size: 16px;
            font-weight: 400;
            color: #0f1111;
            line-height: 1.6;
            font-family: "Amazon Ember", Arial, sans-serif;
            margin: 0;
        }

        .amazon-description .tagline-highlight {
            color: #c45500;
            font-weight: 500;
        }

        /* Amazon-style Login Card */
        .amazon-login-card {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 26px 30px;
            width: 400px;
            max-width: 100%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.13);
        }

        .amazon-login-card .card-header {
            margin-bottom: 18px;
            padding-bottom: 0;
            border-bottom: none;
        }

        .amazon-login-card .card-header h1 {
            font-size: 28px;
            font-weight: 400;
            color: #111;
            margin: 0 0 4px 0;
            line-height: 1.2;
            font-family: "Amazon Ember", Arial, sans-serif;
        }

        .amazon-login-card .card-header p {
            font-size: 13px;
            color: #555;
            margin: 0;
            font-family: "Amazon Ember", Arial, sans-serif;
        }

        .amazon-form-group {
            margin-bottom: 14px;
        }

        .amazon-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #111;
            margin-bottom: 4px;
            font-family: "Amazon Ember", Arial, sans-serif;
        }

        .amazon-input {
            width: 100%;
            height: 36px;
            padding: 4px 10px;
            font-size: 14px;
            border: 1px solid #a6a6a6;
            border-top-color: #949494;
            border-radius: 4px;
            outline: none;
            transition: all 0.1s ease-in-out;
            box-sizing: border-box;
            font-family: "Amazon Ember", Arial, sans-serif;
            background: #fff;
        }

        .amazon-input:focus {
            border-color: #e77600;
            box-shadow: 0 0 3px 2px rgba(228, 121, 17, 0.5);
        }

        .amazon-input::placeholder {
            color: #aaa;
        }

        .amazon-password-container {
            position: relative;
        }

        .amazon-password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px;
            color: #555;
            line-height: 1;
        }

        .amazon-password-toggle:hover {
            color: #111;
        }

        .amazon-checkbox-group {
            display: flex;
            align-items: center;
            margin: 18px 0;
        }

        .amazon-checkbox-group input[type="checkbox"] {
            width: 15px;
            height: 15px;
            margin-right: 8px;
            accent-color: #e77600;
            cursor: pointer;
        }

        .amazon-checkbox-group label {
            font-size: 13px;
            color: #111;
            font-weight: 400;
            cursor: pointer;
            font-family: "Amazon Ember", Arial, sans-serif;
            margin-bottom: 0;
        }

        .amazon-btn-primary {
            width: 100%;
            height: 36px;
            background: linear-gradient(to bottom, #f7dfa5, #f0c14b);
            border: 1px solid;
            border-color: #a88734 #9c7e31 #846a29;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #111;
            font-family: "Amazon Ember", Arial, sans-serif;
            transition: all 0.1s ease-in-out;
        }

        .amazon-btn-primary:hover {
            background: linear-gradient(to bottom, #f5d78e, #eeb933);
        }

        .amazon-btn-primary:active {
            background: #f0c14b;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) inset;
        }

        .amazon-btn-primary:focus {
            outline: none;
            border-color: #e77600;
            box-shadow: 0 0 3px 2px rgba(228, 121, 17, 0.5);
        }

        /* Amazon-style Error Box */
        .amazon-error-box {
            background: #fff;
            border: 1px solid #c40000;
            border-radius: 4px;
            border-left-width: 4px;
            padding: 14px 18px;
            margin-bottom: 18px;
        }

        .amazon-error-box .error-heading {
            color: #c40000;
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
                display: flex;
            align-items: center;
            gap: 8px;
            font-family: "Amazon Ember", Arial, sans-serif;
        }

        .amazon-error-box .error-message {
            color: #111;
            font-size: 13px;
            font-family: "Amazon Ember", Arial, sans-serif;
            margin-left: 28px;
        }

        /* Amazon-style Success Box */
        .amazon-success-box {
            background: #fff;
            border: 1px solid #067d62;
            border-radius: 4px;
            border-left-width: 4px;
            padding: 14px 18px;
            margin-bottom: 18px;
        }

        .amazon-success-box .success-heading {
            color: #067d62;
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 4px;
                display: flex;
            align-items: center;
            gap: 8px;
            font-family: "Amazon Ember", Arial, sans-serif;
        }

        .amazon-success-box .success-message {
            color: #111;
            font-size: 13px;
            font-family: "Amazon Ember", Arial, sans-serif;
            margin-left: 28px;
        }

        /* Footer */
        .amazon-footer {
            margin-top: 30px;
            text-align: center;
        }

        .amazon-footer-divider {
            position: relative;
            margin: 25px 0 15px;
        }

        .amazon-footer-divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, rgba(0,0,0,0), rgba(0,0,0,0.15), rgba(0,0,0,0));
        }

        .amazon-copyright {
            font-size: 11px;
            color: #555;
            margin-top: 10px;
            font-family: "Amazon Ember", Arial, sans-serif;
        }

        /* Responsive Styles */
        @media (max-width: 480px) {
            .amazon-login-wrapper {
                padding: 20px 15px;
            }

            .amazon-logo-card {
                padding: 20px 25px;
                margin-bottom: 15px;
            }

            .amazon-logo-card img {
                max-width: 200px;
            }

            .amazon-login-card {
                width: 100%;
                padding: 20px;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }

            .amazon-description .tagline {
                font-size: 14px;
            }
        }

        @media (min-width: 481px) and (max-width: 767px) {
            .amazon-login-card {
                width: 95%;
                max-width: 400px;
            }

            .amazon-logo-card {
                padding: 25px 40px;
            }

            .amazon-logo-card img {
                max-width: 240px;
            }
        }

        @media (min-width: 768px) {
            .amazon-login-wrapper {
                padding: 50px 20px 20px;
            }
        }
    </style>

    <div class="amazon-login-wrapper">
        <!-- Logo Card Section -->
        <div class="amazon-logo-card">
            @php
                $filename = session('business.logo');
                $fullpath = "/uploads/business_logos/$filename";
            @endphp
            @if ($filename != null)
                <img src="{{ $fullpath }}" alt="{{ session('business.name') }}">
            @else
                <img src="{{ asset('uploads/login/smokevana-logo.png') }}" alt="Smokevana">
            @endif
        </div>

        <!-- Description Section -->
        <div class="amazon-description">
            <p class="tagline">
                Streamlining your <span class="tagline-highlight">business operations</span>, 
                effortless management, and <span class="tagline-highlight">powerful results</span>.
            </p>
        </div>

        <!-- Login Card -->
        <div class="amazon-login-card">
            <!-- Card Header -->
            <div class="card-header">
                <h1>Sign in</h1>
            </div>

            <!-- Success Message -->
            @if (session('status') && session('status.success') == 1)
                <div class="amazon-success-box">
                    <div class="success-heading">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#067d62" stroke="#067d62" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Success
                    </div>
                    <div class="success-message">
                        {{ session('status.msg') }}
        </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any() || (session('status') && session('status.success') == 0))
                <div class="amazon-error-box">
                    <div class="error-heading">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c40000" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        There was a problem
                            </div>
                    <div class="error-message">
                        @if (session('status') && session('status.success') == 0)
                            {{ session('status.msg') }}
                            @endif
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
                                    @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                {{ csrf_field() }}
                
                <!-- Username Field -->
                <div class="amazon-form-group">
                    <label for="username">Username</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="amazon-input" 
                           value="{{ $username }}" 
                           required 
                           autofocus>
                </div>

                <!-- Password Field -->
                <div class="amazon-form-group">
                    <label for="password">Password</label>
                    <div class="amazon-password-container">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="amazon-input" 
                               value="{{ $password }}" 
                               required 
                               style="padding-right: 40px;">
                        <button type="button" id="show_hide_icon" class="amazon-password-toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                </svg>
                            </button>
                    </div>
                    </div>

                <!-- Login Button -->
                <button type="submit" class="amazon-btn-primary">
                    Sign in
                    </button>

                <!-- Remember Me Checkbox -->
                <div class="amazon-checkbox-group">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Keep me signed in</label>
            </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="amazon-footer">
            <div class="amazon-footer-divider"></div>
            <p class="amazon-copyright">© {{ date('Y') }} Smokevana ERP. All rights reserved.</p>
        </div>
    </div>

    @if (config('app.env') == 'demo')
        <div style="position: fixed; bottom: 20px; left: 20px; z-index: 1000;">
            @component('components.widget', [
                'class' => 'box-primary',
                'header' =>
                    '<h4 class="text-center">Demo Shops <small><i> <br/>Demos are for example purpose only</i> <br/><b>Click button to login</b></small></h4>',
            ])
                <a href="?demo_type=all_in_one" class="btn btn-app bg-olive demo-login" data-toggle="tooltip"
                    title="Showcases all feature available in the application."
                    data-admin="{{ $demo_types['all_in_one'] }}">
                    <i class="fas fa-star"></i> All In One</a>
            @endcomponent
        </div>
    @endif
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#show_hide_icon').off('click');
            
            $('a.demo-login').click(function(e) {
                e.preventDefault();
                $('#username').val($(this).data('admin'));
                $('#password').val("{{ $password }}");
                $('form#login-form').submit();
            });

            $('#show_hide_icon').on('click', function(e) {
                e.preventDefault();
                const passwordInput = $('#password');

                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    $('#show_hide_icon').html(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"/><path d="M3 3l18 18"/></svg>'
                    );
                } else if (passwordInput.attr('type') === 'text') {
                    passwordInput.attr('type', 'password');
                    $('#show_hide_icon').html(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>'
                    );
                }
            });
        });
    </script>
@endsection
