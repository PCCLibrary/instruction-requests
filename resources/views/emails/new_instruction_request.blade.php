@extends('emails.master')

@section('title', 'New Instruction Request')

@section('heading', 'New Instruction Request Submitted')

@section('intro')
    A new instruction request has been submitted by {{ $instructor_name }}. Please review the details below and follow the link to edit the request in the dashboard.
@endsection

@section('content')
    <p><a href="{{ route('instructionRequests.edit', ['id' => $request->id]) }}">Edit Request in Dashboard</a></p>
@endsection
