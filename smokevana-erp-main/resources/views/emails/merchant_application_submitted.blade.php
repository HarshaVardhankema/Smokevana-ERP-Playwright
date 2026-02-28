<!DOCTYPE html>
<html>
<head>
    <title>New Merchant Application</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Merchant Application Submitted</h2>
        </div>
        
        <div class="content">
            <p>A new merchant application has been submitted and requires your review.</p>
            
            <h3>Business Information:</h3>
            <ul>
                <li><strong>Legal Business Name:</strong> {{ $application->legal_business_name }}</li>
                <li><strong>DBA Name:</strong> {{ $application->dba_name ?? 'N/A' }}</li>
                <li><strong>Business Type:</strong> {{ $application->business_type }}</li>
                <li><strong>Federal Tax ID:</strong> {{ $application->federal_tax_id }}</li>
                <li><strong>Business Age:</strong> {{ $application->business_age }}</li>
                <li><strong>Business Phone:</strong> {{ $application->business_phone }}</li>
                <li><strong>Website:</strong> {{ $application->website ?? 'N/A' }}</li>
            </ul>

            <h3>Owner Information:</h3>
            <ul>
                <li><strong>Name:</strong> {{ $application->owner_legal_name }}</li>
                <li><strong>Ownership Percentage:</strong> {{ $application->ownership_percentage }}%</li>
                <li><strong>Job Title:</strong> {{ $application->job_title }}</li>
                <li><strong>Email:</strong> {{ $application->owner_email }}</li>
                <li><strong>Phone:</strong> {{ $application->owner_phone }}</li>
            </ul>

            @if($application->additional_owners)
            <h3>Additional Owners Information:</h3>
            <ul>
                @foreach($application->additional_owners as $owner)
                <li><strong>Name:</strong> {{ $owner['name'] }}</li>
                <li><strong>Ownership Percentage:</strong> {{ $owner['percentage'] }}%</li>
                <li><strong>DOB:</strong> {{ $owner['dob'] }}</li>
                <li><strong>SSN:</strong> {{ $owner['ssn'] ?? 'N/A' }}</li>
                @endforeach
            </ul>
            @endif

            @if($application->has_previous_processing)
            <h3>Previous Processing Information:</h3>
            <ul>
                <li><strong>Duration:</strong> {{ $application->processing_duration }}</li>
                <li><strong>Previous Processor:</strong> {{ $application->previous_processor }}</li>
                <li><strong>Average Ticket Amount:</strong> ${{ number_format($application->average_ticket_amount, 2) }}</li>
                <li><strong>Monthly Volume:</strong> ${{ number_format($application->monthly_volume, 2) }}</li>
            </ul>
            @endif
        </div>

        <div class="footer">
            <p>This is an automated message. so all replies will be considered only in this mails reply. Please do not reply with any new mails. Thank you.</p>
        </div>
    </div>
</body>
</html> 