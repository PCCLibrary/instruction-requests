@extends('emails.master')

@section('title', 'Accepted Instruction Request')

@section('heading', 'Accepted Instruction Request')

@section('intro')
    The instruction request by {{ $instructor_name }} has been accepted. Please review the details below and follow the link to edit the request in the dashboard.
@endsection

@section('content')
    <p><a href="{{ route('instructionRequests.edit', ['id' => $request->id]) }}">Edit Request in Dashboard</a></p>
@endsection
