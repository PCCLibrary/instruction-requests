<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instruction Requests</title>

    <link rel="stylesheet" id="pcc-library-style-css" href="https://www.pcc.edu/library/wp-content/themes/Lib2019/assets/css/styles.css?ver=6.4.1" type="text/css" media="all">

    <link href="{{ asset('css/public.css') }}" rel="stylesheet">

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js?ver=3.1.1" id="jquery-core-js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
                <ol class="breadcrumb bg-secondary m-0 p-0">
                    <li class="breadcrumb-item"><a class="text-white" href="https://www.pcc.edu/library/">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Library Instruction Request</li>
                </ol>
            </nav>
        </div>
    </div>
    <section class="w-100 m-0 py-4 py-lg-6 py-xl-6 bg-light mb-4">
        <div class="container">
            <div class="align-items-center">

                <div class="py-4 col-lg-8">
                    <h1 class="display-4 mt-0 text-blue">Library Instruction Request</h1>

                </div>

            </div>
        </div>
    </section>

<div class="container mt-5">

    @include('flash::message')

    @include('adminlte-templates::common.errors')

    <div class="content-wrapper">

        @if(session('success'))

        <section id="success-message" class="card alert-info mb-4">
            <div class="card-body">
                <p><strong>Thank you for requesting a library instruction session. A librarian will contact you soon to confirm your reservation.</strong></p>

                <p>Meanwhile, here are a few ways you and your students can get the most from our time together.</p>
                <ul>
                    <li>Please prepare your students in advance of the library session by emphasizing the importance of research skills and letting them know where and when instruction will take place.</li>
                    <li>Plan to attend the entire session with your students. Successful library instruction depends upon active, in-class collaboration between the librarian and you as the content expert. We rely on you throughout the session to provide context for your students' needs.</li>
                </ul>
            </div>
        </section>

            <section id="form_notice" class="card bg-primary mb-4 alert-dismissible">

                <div class="card-body">

                <p class="p-0 m-0">We left your data in the form in case you need to submit a similar request. Please update as needed and submit. If you are done, you can leave this page.</p>

                    <p></p>
                </div>
            </section>

        @endif

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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/html-duration-picker@latest/dist/html-duration-picker.min.js"></script>

<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script src="{{ asset('js/public.js') }}"></script>

</body>
</html>
