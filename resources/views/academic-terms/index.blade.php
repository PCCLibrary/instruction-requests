@extends('layouts.app')

@section('page-title', 'Academic Terms Management')
@section('page-description', 'Define term dates for instruction scheduling and reporting')

@section('content')
    <div class="pt-2 pb-4">
        @livewire('academic-terms-manager')
    </div>
@endsection
