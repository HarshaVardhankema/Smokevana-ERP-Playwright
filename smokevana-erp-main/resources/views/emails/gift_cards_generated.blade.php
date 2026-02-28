<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Gift Cards</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #FF9900;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #232f3e;
            margin: 0;
            font-size: 28px;
        }
        .gift-card {
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            position: relative;
        }
        .gift-card-code {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            background-color: #e7f3ff;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            margin: 15px 0;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }
        .gift-card-amount {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 10px 0;
        }
        .gift-card-expiry {
            font-size: 14px;
            color: #6c757d;
            text-align: center;
            margin: 5px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #FF9900;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 0;
        }
        .btn:hover {
            background-color: #e67e00;
        }
        .important-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎁 Your Gift Cards Have Arrived!</h1>
            <p>Thank you for your order #{{ $order_number }}</p>
        </div>

        <p>Dear {{ $customer_name }},</p>
        
        <p>Great news! Your gift cards from order #{{ $order_number }} (Invoice #{{ $invoice_number }}) have been generated and are ready to use.</p>

        @if(!empty($gift_cards))
            @foreach($gift_cards as $giftCard)
                <div class="gift-card">
                    <h3>{{ $giftCard['product_name'] }}</h3>
                    
                    <div class="gift-card-amount">
                        ${{ number_format($giftCard['amount'], 2) }}
                    </div>
                    
                    <div class="gift-card-code">
                        {{ $giftCard['code'] }}
                    </div>
                    
                    @if(!empty($giftCard['expires_at']))
                        <div class="gift-card-expiry">
                            <strong>Expires:</strong> {{ \Carbon\Carbon::parse($giftCard['expires_at'])->format('F j, Y') }}
                        </div>
                    @endif
                    
                    <p style="text-align: center; margin-top: 15px;">
                        <strong>Important:</strong> Keep this code safe. You'll need it to redeem your gift card.
                    </p>
                </div>
            @endforeach
        @endif

        <div class="important-note">
            <h4>📌 How to Use Your Gift Card:</h4>
            <ol>
                <li>Copy the gift card code shown above</li>
                <li>Visit our website or store</li>
                <li>Apply the code during checkout</li>
                <li>The amount will be deducted from your total</li>
            </ol>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}" class="btn">Start Shopping</a>
        </div>

        <p>If you have any questions about your gift cards or need assistance, please don't hesitate to contact our customer support team at {{ $support_email }}.</p>

        <div class="footer">
            <p><strong>{{ $business_name }}</strong></p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you didn't expect this email, please contact our support team immediately.</p>
        </div>
    </div>
</body>
</html>
