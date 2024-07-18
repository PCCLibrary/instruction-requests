@extends('emails.master')

@section('title', 'Assigned Instruction Request')

@section('heading', 'An Instruction Request Has Been Assigned to You')

@section('intro')
    An instruction request has been assigned to you by {{ $instructor_name }}. Please review the details below and follow the link to edit the request in the dashboard.
@endsection

@section('content')
    <p><a href="{{ route('instructionRequests.edit', ['id' => $request->id]) }}">Edit Request in Dashboard</a></p>
@endsection
