<!DOCTYPE html>
<html>
<head>
    <title>Merchant Application Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Merchant Application Status Update</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $application->owner_legal_name }},</p>

            <p>Your merchant application for <strong>{{ $application->legal_business_name }}</strong> has been reviewed.</p>

            <p class="status-{{ $application->status }}">
                Status: {{ ucfirst($application->status) }}
            </p>

            <h3>Review Notes:</h3>
            <div style="background-color: #f8f9fa; padding: 15px; margin: 15px 0;">
                {{ $application->admin_notes }}
            </div>

            <h3>Response:</h3>
            <div style="background-color: #f8f9fa; padding: 15px; margin: 15px 0;">
                {{ $application->admin_response }}
            </div>

            @if($application->status === 'approved')
            <p>Congratulations! Your application has been approved. Our team will contact you shortly with next steps.</p>
            @else
            <p>If you have any questions about this decision or would like to provide additional information, please contact our support team.</p>
            @endif

            <p>Thank you for your interest in our services.</p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>For support, please contact our merchant services team.</p>
        </div>
    </div>
</body>
</html> 