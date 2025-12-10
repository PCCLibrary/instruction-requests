@extends('layouts.app')

@section('page-title')
    Comparison Analysis
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 ml-3">
        Coming Soon
    </span>
@endsection

@section('page-description', 'Compare metrics across campuses and departments for strategic planning.')

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-8 text-center">
        <div class="flex justify-center mb-6">
            <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full">
                <x-heroicon-o-arrows-right-left class="w-16 h-16 text-gray-400 dark:text-gray-500" />
            </div>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Feature Coming Soon
        </h2>

        <div class="max-w-2xl mx-auto space-y-4 text-gray-600 dark:text-gray-300">
            <p class="text-lg">
                The Comparison Analysis feature is currently in development.
            </p>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-left">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">What to expect:</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-start">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                        <span>Side-by-side campus comparison views</span>
                    </li>
                    <li class="flex items-start">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                        <span>Department and librarian workload distribution</span>
                    </li>
                    <li class="flex items-start">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                        <span>Modality breakdown (synchronous, asynchronous, remote)</span>
                    </li>
                    <li class="flex items-start">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                        <span>Strategic insights for resource allocation and planning</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('statistics.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                Back to Statistics
            </a>
        </div>
    </div>
</div>
@endsection
