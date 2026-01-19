@extends('layouts.app')

@section('page-title', 'Summary Statistics')
@section('page-description', 'Aggregated statistics with dimensional breakdowns.')

@section('content')
<livewire:statistics.summary-statistics />
@endsection
