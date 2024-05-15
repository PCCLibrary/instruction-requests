<!DOCTYPE html>
<html>
<head>
    <title>New Instruction Request</title>
</head>
<body>
<h1>New Instruction Request Submitted</h1>
<p>A new instruction request has been submitted by {{ $request->faculty_name }}. Please review the details below and follow the link to edit the request in the dashboard.</p>
<ul>
    @foreach ($request->toArray() as $field => $value)
        @if (!empty($value))
            <li><strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong> {{ $value }}</li>
        @endif
    @endforeach
</ul>
<p><a href="{{ route('instructionRequests.edit', ['id' => $request->id]) }}">Edit Request in Dashboard</a></p>
<p>Thank you,<br>PCC Library Instruction System</p>
</body>
</html>
