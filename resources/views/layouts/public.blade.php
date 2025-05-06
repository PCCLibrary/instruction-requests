<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instruction Requests</title>

{{--    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">--}}

{{--    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">--}}

    <link rel="stylesheet" id="pcc-library-style-css" href="/library/wp-content/themes/Lib2024/assets/css/styles.css" type="text/css" media="all">

</head>
<body class="bg-gradient-gray" data-template="base.twig" lang="en-US">

<main role="main" id="main" aria-label="Content">

    <div class="container my-5">

    <div class="content-wrapper">

        @yield('content')

    </div>
</div>

</main>


{{--@vite(['resources/js/public.js'])--}}

</body>
</html>
