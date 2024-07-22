@extends('emails.master')

@section('title', 'New Instruction Request')

@section('heading', 'New Instruction Request Submitted')

@section('intro')
    A new instruction request has been submitted by {{ $request->instructor_name }}. Please review the details below and follow the link to edit the request in the dashboard.
@endsection

@section('content')
    <x-link-button :url="route('instructionRequests.edit', ['id' => $request->id])">
        Edit Request in Dashboard
    </x-link-button>
@endsection
