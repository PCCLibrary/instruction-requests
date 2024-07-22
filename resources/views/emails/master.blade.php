<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
</head>
<body>
<h1>@yield('heading')</h1>
<p>@yield('intro')</p>

@include('emails.fields.' . $request->instruction_type . '_fields', ['request' => $request])

@yield('content')

<p>Thank you,<br>PCC Librarians</p>
</body>
</html>
