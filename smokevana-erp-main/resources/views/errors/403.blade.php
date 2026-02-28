<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>403 - Access Denied | {{ Session::get('business.name', config('app.name')) }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }
        
        .error-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }
        
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
        
        .error-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
            box-shadow: 0 10px 30px rgba(238, 90, 111, 0.4);
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .error-icon svg {
            width: 60px;
            height: 60px;
            fill: #ffffff;
        }
        
        .error-title {
            font-size: 28px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
        }
        
        .error-description {
            font-size: 16px;
            color: #a0aec0;
            line-height: 1.6;
            margin-bottom: 40px;
            padding: 20px;
            border-radius: 12px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 32px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .btn-secondary {
            background: #ffffff;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #f7fafc;
            transform: translateY(-2px);
        }
        
        @media (max-width: 640px) {
            .error-container {
                padding: 40px 30px;
            }
            
            .error-title {
                font-size: 24px;
            }
            
            .error-icon {
                width: 100px;
                height: 100px;
            }
            
            .error-icon svg {
                width: 50px;
                height: 50px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
            </svg>
        </div>
        
        
        <h1 class="error-title">Access Denied</h1>
        
        <div class="error-description">
            <strong>What does this mean?</strong><br>
            You're trying to access a resource or perform an action that requires specific permissions. 
            Please contact your administrator if you believe you should have access to this feature.
        </div>
        
        <div class="action-buttons">
            @if(auth()->check())
                <a href="{{ url('/home') }}" class="btn btn-primary">
                    Go to Dashboard
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    Go Back
                </a>
            @else
                <a href="{{ url('/login') }}" class="btn btn-primary">
                    Login
                </a>
            @endif
        </div>
    </div>
</body>
</html>

