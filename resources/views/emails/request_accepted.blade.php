@extends('emails.master')

@section('title', 'Accepted Instruction Request')

@section('heading', 'Accepted Instruction Request')

@section('intro')
    The instruction request has been accepted. Please review the details below and follow the link to edit the request in the dashboard.
@endsection

@section('content')
    <x-link-button :url="route('instructionRequests.edit', ['id' => $request->id])">
        Edit Request in Dashboard
    </x-link-button>
@endsection
