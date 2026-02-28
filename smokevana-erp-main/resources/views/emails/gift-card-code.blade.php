<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Gift Card Code</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #F0F0F0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .email-wrapper {
            width: 600px;
            background-color: #fff;
            text-align: center;
            margin: 20px;
        }

        .header img {
            width: 100%;
            height: auto;
            display: block;
        }

        .email-container {
            width: 100%;
            background-image: url('{{ asset('img/gsw_emails-07_body.jpg') }}');
            background-size: cover;
            background-position: center;
            padding: 30px 0;
        }

        .email-content {
            padding: 20px;
            color: #000000;
            margin-top: 50px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
        }

        .email-content p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #000000;
        }

        .gift-card-code {
            background-color: #FFFFFF;
            border: 2px solid #FF9929;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #000000;
            font-family: 'Courier New', monospace;
        }

        .gift-card-details {
            background-color: #F5F5F5;
            border: 1px solid #DDDDDD;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }

        .gift-card-details p {
            margin: 8px 0;
            font-size: 14px;
            color: #000000;
        }

        .footer {
            font-size: 12px;
            color: #000000;
            margin-top: 20px;
            padding-bottom: 10px;
        }

        @media (max-width: 600px) {
            .email-wrapper {
                width: 90vw;
                padding: 0 15px;
            }

            .gift-card-code {
                font-size: 18px;
                letter-spacing: 2px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <img src="{{asset('img/gsw_emails_header-08.jpg')}}" alt="Header Image" style="width: 100%; height: auto;" />
            
            <div class="email-container">
                <div class="email-content">
                    <p style="color: #000000;">
                        Hi <strong style="font-weight: 800; color: #000000;">{{$customer_name ?? 'Customer'}}</strong>,
                    </p>
                    <p style="color: #000000;">
                        Thank you for selecting a gift card! Here is your gift card code:
                    </p>
                    
                    <div class="gift-card-code">
                        {{$gift_card_code ?? 'N/A'}}
                    </div>

                    <div class="gift-card-details">
                        <p style="color: #000000;"><strong style="color: #000000;">Gift Card Balance:</strong> {{$currency ?? 'USD'}} {{number_format($balance ?? 0, 2)}}</p>
                        <p style="color: #000000;"><strong style="color: #000000;">Initial Amount:</strong> {{$currency ?? 'USD'}} {{number_format($initial_amount ?? 0, 2)}}</p>
                        @if(!empty($expires_at))
                        <p style="color: #000000;"><strong style="color: #000000;">Expires:</strong> {{$expires_at}}</p>
                        @endif
                        @if(!empty($gift_card_message))
                        <p style="color: #000000;"><strong style="color: #000000;">Message:</strong> {{$gift_card_message}}</p>
                        @endif
                    </div>

                    <p style="color: #000000;">
                        You can use this code during checkout to apply the gift card balance to your order.
                    </p>

                    <p style="font-size: 12px; margin-top: 20px; color: #000000;">
                        <strong style="color: #000000;">Important:</strong> Keep this code secure. Do not share it with others.
                    </p>

                    <div class="footer">
                        &copy; {{ date('Y') }} {{ $app_name ?? 'Smokevana' }}. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
