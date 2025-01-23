<!-- resources/views/campuses/create.blade.php -->
@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Create Campus') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form action="{{ route('campuses.store') }}" method="POST" class="p-6">
                    @csrf

                    @include('campuses.partials.    fields')

                    <div class="gap-4 mt-6">
                        <x-editor-actions
                            route="{{ route('campuses.index') }}"
                            :showBack="false"
                        />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
