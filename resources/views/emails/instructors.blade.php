<!DOCTYPE html>
<html>
<head>
    <title>Instruction Request Confirmation</title>
</head>
<body>
<h1>Your Instruction Request Has Been Received</h1>
<p>Dear {{ $request->faculty_name }},</p>
<p>Thank you for submitting your instruction request. Here are the details of your request:</p>
<ul>
    @foreach ($request->toArray() as $field => $value)
        @if (!empty($value))
            <li><strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong> {{ $value }}</li>
        @endif
    @endforeach
</ul>
<p>Our team will review your request and get back to you shortly.</p>
<p>Best regards,<br>PCC Library Instruction Team</p>
</body>
</html>
