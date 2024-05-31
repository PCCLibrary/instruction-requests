@extends('layouts.public')

@section('content')

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

    @include('flash::message')

    @include('adminlte-templates::common.errors')

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
            </div>
        </section>

    @endif

    <section class="content px-3 mb-4">

        <div class="card">

            @include('public_form.fields')

        </div>
    </section>
@endsection
