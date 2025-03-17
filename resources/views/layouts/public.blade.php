<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instruction Requests</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
{{--    <link rel="stylesheet" id="pcc-library-style-css" href="https://www.pcc.edu/library/wp-content/themes/Lib2024/assets/css/styles.css" type="text/css" media="all">--}}

    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

{{--    <link href="{{ asset('css/public.css') }}" rel="stylesheet">--}}

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js?ver=3.1.1" id="jquery-core-js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>
        // Disable Dropzone auto-discover before any DOM is loaded
        if (typeof Dropzone !== 'undefined') {
            Dropzone.autoDiscover = false;
        }
        // Define base URL for API calls
        var baseUrl = '{{ url('/') }}';
    </script>

    <style>
        .is-required:after {
            content: '*';
            color: red;
            font-size: .9em;
        }
    </style>
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

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        {{--                    <h1>Library Instruction Request</h1>--}}
                        <p>Thank you for your interest in library instruction for your class. Faculty librarians provide information literacy instruction for a large number of classes at PCC’s campuses, centers, and work sites.</p>

                        <p>Librarians can: provide synchronous instruction to face-to-face, online, and hybrid and remote classes; or develop asynchronous instructional tools like videos, tutorials, and research guides tailored to your assignment class.</p>

                        <p>Please fill out the form below <strong>at least one week in advance</strong> of your requested date. The development of some asynchronous online learning objects may require more advanced notice.</p>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="alert alert-success bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                <h3 class="font-bold p-0 m-0">Success</h3>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                <h3 class="font-bold p-0 m-0">Error</h3>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded shadow-sm" role="alert">
                <h3 class="font-bold p-0 m-0">Info</h3>
                <p>{{ session('info') }}</p>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm" role="alert">
                <h3 class="font-bold p-0 m-0">Warning</h3>
                <p>{{ session('warning') }}</p>
            </div>
        @endif



        <!-- Display Errors -->
        @if($errors->any())
            <div class="bg-red-100 text-red-900 p-4 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <section id="success-message" class="card alert-info mb-4">
                <div class="card-body">
                    <p><strong>Thank you for requesting a library instruction session. A librarian will contact you soon to
                            confirm your reservation.</strong></p>

                    <p>Meanwhile, here are a few ways you and your students can get the most from our time together.</p>
                    <ul>
                        <li>Please prepare your students in advance of the library session by emphasizing the importance of
                            research skills and letting them know where and when instruction will take place.
                        </li>
                        <li>Plan to attend the entire session with your students. Successful library instruction depends
                            upon active, in-class collaboration between the librarian and you as the content expert. We rely
                            on you throughout the session to provide context for your students' needs.
                        </li>
                    </ul>
                </div>
            </section>

            <section id="form_notice" class="card bg-primary mb-4 alert-dismissible">

                <div class="card-body">

                    <p class="p-0 m-0">We left your data in the form in case you need to submit a similar request. Please
                        update as needed and submit. If you are done, you can leave this page.</p>
                </div>
            </section>

        @endif

        @yield('content')
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

@vite(['resources/js/public.js'])

</body>
</html>
