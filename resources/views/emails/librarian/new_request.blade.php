@extends('emails.layouts.base')

@section('content')
    <a href="{{ $request->dashboard_url }}" class="button">View in Dashboard</a>
@endsection
