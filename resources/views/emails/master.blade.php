<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
</head>
<body>
<h1>@yield('heading')</h1>
<p>@yield('intro')</p>

{{-- Summary Information --}}
<ul>
    <li><strong>Instructor:</strong> {{ $instructor_name }}</li>
    <li><strong>Date Requested:</strong> {{ \Carbon\Carbon::parse($date_requested)->format('d/m/Y') }}</li>
    <li><strong>Campus:</strong> {{ $campus_name }}</li>
</ul>

{{-- Detailed Request Fields --}}
<x-notification-fields :instructionRequest="$request" />

@yield('content')

<p>Thank you,<br>PCC Library Instruction System</p>
</body>
</html>
