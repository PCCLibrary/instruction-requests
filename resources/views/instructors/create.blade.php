
<!-- resources/views/instructors/create.blade.php -->
@extends('layouts.app')

@section('page-title', 'Create Instructor')

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form action="{{ route('instructors.store') }}" method="POST" class="p-6">
                    @csrf

                    @include('instructors.partials.fields')

                    <div class="flex items-center gap-4 mt-6">

                        <x-editor-actions
                            route="{{ route('instructors.index') }}"
                            :showBack="(bool)$instructor"
                        />
                        {{--                        <x-form-button type="submit" class="bg-blue-500 hover:bg-blue-600">--}}
                        {{--                            Create Instructor--}}
                        {{--                        </x-form-button>--}}
                        {{--                        <x-form-button href="{{ route('instructors.index') }}" class="bg-gray-300 hover:bg-gray-400">--}}
                        {{--                            Cancel--}}
                        {{--                        </x-form-button>--}}
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
