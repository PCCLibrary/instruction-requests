@extends('emails.layouts.base')

@section('content')
    <p>This request has been accepted.</p>
    <a href="{{ $request['dashboard_url'] }}" class="button">View in Dashboard</a>
@endsection
