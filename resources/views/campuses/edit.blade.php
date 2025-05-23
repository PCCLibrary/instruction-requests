<!-- resources/views/campuses/edit.blade.php -->
@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Campuses', 'route' => 'campuses.index'],
            ['label' => 'Edit Campus'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Editing Campus: <span class="font-weight-lighter text-gray-500 dark:text-gray-300">{{ $campus->name }}</span>
    </h1>

@endsection

@section('content')

    <form action="{{ route('campuses.update', $campus) }}" method="POST" class="p-4 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
        @csrf
        @method('PUT')

        @include('campuses.partials.fields')

        <div class="gap-4 mt-6">
            <x-editor-actions
                route="{{ route('campuses.index') }}"
                :showBack="(bool)$campus"
            />
        </div>
    </form>

    <!-- Delete Section -->
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">
                Delete Campus
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                This will hide the campus from dropdowns and prevent new requests.
                Existing instruction requests will remain intact.
            </p>

            <button
                type="button"
                id="delete-campus-btn"
                data-campus-id="{{ $campus->id }}"
                data-delete-impact-url="{{ route('campuses.deleteImpact', $campus->id) }}"
                class="inline-flex items-center px-3 py-2 border border-red-300 dark:border-red-600 rounded-md shadow-sm text-sm font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Campus
            </button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="mt-3">
                <!-- Warning Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>

                <!-- Modal Content -->
                <div class="mt-4 text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modal-title">
                        Delete Campus?
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-300" id="modal-description">
                            Loading impact analysis...
                        </p>
                    </div>

                    <!-- Impact Details -->
                    <div id="impact-details" class="mt-4 text-left bg-gray-50 dark:bg-gray-700 rounded-lg p-3 hidden">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">This will affect:</h4>
                        <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                            <li id="requests-impact">• <span class="font-medium">X</span> total instruction requests</li>
                            <li id="active-impact">• <span class="font-medium">X</span> active/scheduled requests</li>
                            <li id="librarians-impact">• <span class="font-medium">X</span> assigned librarians</li>
                        </ul>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            * Existing requests will remain accessible for historical purposes
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-3 mt-6">
                    <button
                        id="cancel-delete"
                        type="button"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                        Cancel
                    </button>
                    <form id="delete-form" method="POST" action="{{ route('campuses.destroy', $campus->id) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white text-sm font-medium rounded-md hover:bg-red-700 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-offset-gray-800">
                            Delete Campus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @vite('resources/js/campus-edit.js')
@endpush
