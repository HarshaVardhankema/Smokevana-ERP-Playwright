<!DOCTYPE html>
<html>
<head>
    <title>New Contact Us Message</title>
</head>
<body>
    <p><strong>Full Name:</strong> {{ $data['full_name'] }}</p>
    <p><strong>Email Address:</strong> {{ $data['email'] }}</p>
    <p><strong>Subject:</strong> {{ $data['subject'] ?? 'N/A' }}</p>
    <p><strong>Message:</strong> {{ $data['message'] ?? 'N/A' }}</p>

</body>
</html>
