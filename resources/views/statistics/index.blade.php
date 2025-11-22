@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Statistics Dashboard']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Statistics Dashboard
    </h1>
    <p class="text-gray-600 dark:text-gray-300">Instruction request metrics and reporting.</p>

@endsection

@section('content')
<livewire:statistics.dashboard />
@endsection
