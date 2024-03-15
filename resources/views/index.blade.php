@extends('layouts.public')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
{{--                    <h1>Library Instruction Request</h1>--}}
                    <p>Thank you for your interest in library instruction for your class. Faculty librarians provide information literacy instruction for a large number of classes at PCC’s campuses, centers, and work sites. Librarians can provide synchronous instruction to face-to-face, online, and hybrid classes; develop asynchronous instructional tools like videos, tutorials, and research guides tailored to your assignment; or embed in your Brightspace classroom to support student research.</p><p>Please fill out the form below <strong>at least one week in advance</strong> of your requested date. The development of some asynchronous online learning objects may require more advanced notice.</p>
                </div>
            </div>
        </div>
    </section>

{{--    <ul>--}}
{{--        @foreach(session()->all() as $key => $value)--}}
{{--            <li>{{ $key }}: {{ print_r($value, true) }}</li>--}}
{{--        @endforeach--}}
{{--    </ul>--}}


    <div class="content px-3 mb-4">

        <div class="card">

            {!! Form::open(['route' => 'public.instruction-request.store', 'id' => 'instructionRequestForm', 'files' => true]) !!}

            <div class="card-body">

                @include('public_form.fields')

            </div>

            <div class="card-footer">
                {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
{{--                <a href="{{ route('public.instruction-request.create') }}" class="btn btn-info">Cancel</a>--}}
                <button class="btn btn-warning ml-2" id="clearForm" type="button">Clear Form</button>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
