{{-- resources/views/emails/base.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $emailSubject }}</title>
    <style>
        /* Email-safe responsive styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            background-color: #f7f7f7;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
        }
        .header {
            background-color: #008099;
            color: #ffffff;
            padding: 20px;
            border-radius: 0;
            margin: -20px -20px 20px -20px;
        }
        .button {
            display: inline-block;
            background-color: #008099;
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #005362;
        }
        .details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                padding: 15px;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div style="background-color: {{ $headerColor ?? '#008099' }}; height: 8px; margin: -20px -20px 0 -20px;"></div>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;">
            {{ $emailSubject }}
        </h1>
    </div>

    <div class="details">
        @include('emails.partials.common_fields', ['request' => $request])

        @php
            $typeTemplate = in_array($request['instruction_type'], ['remote', 'on-campus', 'asynchronous'])
                ? "emails.partials.{$request['instruction_type']}_fields"
                : 'emails.partials.request_details';
        @endphp

        @includeWhen(View::exists($typeTemplate), $typeTemplate, ['request' => $request])
    </div>

    @yield('content')

    {{--    @yield('dashboard_link')--}}

    <div class="footer">
        <p>Thank you,<br>PCC Librarians</p>
        <p style="font-size: 10px; color: #999; margin-top: 20px;">
            Library Instruction Request System
        </p>
    </div>
</div>
</body>
</html>
