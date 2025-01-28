@extends('emails.layouts.base')

@section('content')
    <p>This request has been assigned to you.</p>
    <a href="{{ $request['dashboard_url'] }}" class="button">View in Dashboard</a>
@endsection
