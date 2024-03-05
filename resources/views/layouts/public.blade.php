<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instruction Requests</title>

    <link rel="stylesheet" id="pcc-library-style-css" href="https://www.pcc.edu/library/wp-content/themes/Lib2019/assets/css/styles.css?ver=6.4.1" type="text/css" media="all">
{{--    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">--}}
{{--    <link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}

</head>
<body class="page-template page-template-page-no-sidebar page-template-page-no-sidebar-php page page-instruction-request" data-template="base.twig" lang="en-US">

<main role="main" id="main" aria-label="Content">

    <div class="w-100 p-4" style="background-color: #008099;">
    <div class="container">
        <div class="col-12">
            <h1 class="text-white">Library Navigation</h1>
            <p class="text-white lead">this is a placeholder - it will be the regular library site navigation</p>
        </div>
    </div>
</div>
    <div class="bg-secondary p-4">
        <div class="container">
            <nav class="m-0 col-12" aria-label="breadcrumb">
                <ol class="breadcrumb bg-secondary m-0">
                    <li class="breadcrumb-item"><a class="text-white" href="https://www.pcc.edu/library/">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Library Instruction Request</li>
                </ol>
            </nav>
        </div>
    </div>
    <section class="w-100 m-0 py-4 py-lg-6 py-xl-6 bg-light mb-4">
        <div class="container">
            <div class="row align-items-center">

                <div class="py-4 col-lg-8">
                    <h1 class="display-4 mt-0 text-blue">Library Instruction Request</h1>

                </div>

            </div>
        </div>
    </section>
<div class="container mt-5">

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="content-wrapper">
        <section class="content">
            @yield('content')
        </section>
    </div>
</div>

</main>

<footer class="w-100 bg-dark p-4">
    <div class="container">
        <div class="col-12 d-flex justify-content-center">
            <strong class="text-white">Library Footer</strong>
        </div>
    </div>
</footer>
<!-- Bootstrap Bundle with Popper -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
