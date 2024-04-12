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

    <div class="content px-3 mb-4">

        <div class="card">

            @include('public_form.fields')

        </div>
    </div>
@endsection
