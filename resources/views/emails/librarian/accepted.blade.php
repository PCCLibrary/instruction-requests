@extends('emails.base')

@section('content')
    <p>This request has been accepted.</p>
@endsection

@section('dashboard_link')
    <p style="margin: 20px 0;">
        <a href="{{ $dashboardUrl }}" class="button">View Request Details</a>
    </p>
@endsection
