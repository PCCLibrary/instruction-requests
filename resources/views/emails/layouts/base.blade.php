<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
</head>
<body>
<h1>{{ $heading ?? 'Default Heading' }}</h1>

@if(isset($intro))
    <p>{{ $intro }}</p>
@endif

@if(View::exists('emails.partials.' . $request['instruction_type'] . '_fields'))
    @include('emails.partials.' . $request['instruction_type'] . '_fields', ['request' => $request])
@else
    <p>Instruction details are unavailable.</p>
@endif

@yield('content')

<p>Thank you,<br>PCC Librarians</p>
</body>
</html>
