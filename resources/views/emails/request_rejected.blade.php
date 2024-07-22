@extends('emails.master')

@section('title', 'Rejected Instruction Request')

@section('heading', 'Rejected Instruction Request')

@section('intro')
    The instruction request has been rejected. Please review the details below.
@endsection

@section('content')
    <x-link-button :url="route('instructionRequests.edit', ['id' => $request->id])">
        Edit Request in Dashboard
    </x-link-button>
@endsection
