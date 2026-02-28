<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vendor Login - Smokevana</title>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --amazon-navy: #232f3e;
            --amazon-navy-light: #37475a;
            --amazon-orange: #ff9900;
            --amazon-orange-hover: #e88b00;
            --gray-100: #f7f8f8;
            --gray-200: #e6e6e6;
            --gray-300: #d5d9d9;
            --gray-500: #888c8c;
            --gray-600: #565959;
            --gray-700: #393939;
            --gray-800: #222;
            --amazon-link: #007185;
            --amazon-error: #c40000;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Amazon Ember', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f7f8f8 0%, #e8eaed 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 24px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: var(--amazon-orange);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(255, 153, 0, 0.3);
        }

        .logo-icon span {
            font-size: 28px;
            font-weight: 700;
            color: var(--amazon-navy);
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: var(--amazon-navy);
        }

        .logo-text span {
            color: var(--amazon-orange);
        }

        .logo-subtitle {
            font-size: 14px;
            color: var(--gray-600);
            margin-top: 4px;
        }

        /* Login Card */
        .login-card {
            background: #fff;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .login-card h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 20px;
        }

        /* Error Alert */
        .alert-error {
            background: #fff;
            border: 1px solid #c40000;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-error-icon {
            width: 24px;
            height: 24px;
            background: var(--amazon-error);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .alert-error-icon i {
            color: #fff;
            font-size: 12px;
        }

        .alert-error-text {
            font-size: 13px;
            color: var(--amazon-error);
            line-height: 1.4;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.15s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--amazon-orange);
            box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15);
        }

        .form-group input::placeholder {
            color: var(--gray-500);
        }

        /* Checkbox */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--amazon-orange);
            cursor: pointer;
        }

        .checkbox-group label {
            font-size: 13px;
            color: var(--gray-700);
            cursor: pointer;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(to bottom, #f7dfa5, #f0c14b);
            border: 1px solid #a88734;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-800);
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: linear-gradient(to bottom, #f5d78e, #eeba37);
        }

        .btn-submit:active {
            background: linear-gradient(to bottom, #eeba37, #e5a93d);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-300);
        }

        .divider span {
            padding: 0 16px;
            font-size: 12px;
            color: var(--gray-500);
        }

        /* Info Text */
        .info-text {
            text-align: center;
            font-size: 13px;
            color: var(--gray-600);
        }

        .info-text a {
            color: var(--amazon-link);
            text-decoration: none;
        }

        .info-text a:hover {
            text-decoration: underline;
            color: var(--amazon-orange);
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--gray-500);
        }

        .login-footer a {
            color: var(--amazon-link);
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        /* Decorative Elements */
        .decoration {
            position: fixed;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,153,0,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .decoration.top-right {
            top: -100px;
            right: -100px;
        }

        .decoration.bottom-left {
            bottom: -100px;
            left: -100px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 20px;
            }

            .logo-text {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="decoration top-right"></div>
    <div class="decoration bottom-left"></div>

    <div class="login-container">
        <!-- Logo -->
        <div class="logo-section">
            <div class="logo-icon">
                <span>S</span>
            </div>
            <div class="logo-text">Smokevana<span>Central</span></div>
            <div class="logo-subtitle">Vendor Portal</div>
        </div>

        <!-- Login Card -->
        <div class="login-card">
            <h1>Sign In</h1>

            @if($errors->any())
            <div class="alert-error">
                <div class="alert-error-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="alert-error-text">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error">
                <div class="alert-error-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="alert-error-text">{{ session('error') }}</div>
            </div>
            @endif

            @if(session('success'))
            <div class="alert-error" style="border-color: #067d62;">
                <div class="alert-error-icon" style="background: #067d62;">
                    <i class="bi bi-check"></i>
                </div>
                <div class="alert-error-text" style="color: #067d62;">{{ session('success') }}</div>
            </div>
            @endif

            <form action="{{ route('vendor.login.submit') }}" method="POST" id="login-form">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="Enter your email" 
                           value="{{ old('email') }}"
                           autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your password"
                           autocomplete="current-password">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Keep me signed in</label>
                </div>

                <button type="submit" class="btn-submit" id="submit-btn">
                    <span>Sign In</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="divider">
                <span>Need help?</span>
            </div>

            <p class="info-text">
                If you're having trouble accessing your account,<br>
                please <a href="mailto:info@smokevana.com">contact support</a>.
            </p>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <p>&copy; {{ date('Y') }} Smokevana. All rights reserved.</p>
            <p style="margin-top: 8px;">
                <a href="#">Terms of Service</a> &nbsp;·&nbsp; 
                <a href="#">Privacy Policy</a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            var btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Signing in...';
        });
    </script>
</body>
</html>
