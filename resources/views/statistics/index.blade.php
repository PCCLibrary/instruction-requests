@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Statistics & Reporting']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Statistics & Reporting
    </h1>
    <p class="text-gray-600 dark:text-gray-300">Choose from three reporting tools to analyze your instruction program data.</p>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Trends Over Time Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <x-heroicon-o-chart-bar class="w-8 h-8 text-blue-600 dark:text-blue-300" />
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Trends Over Time</h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Available Now
                            </span>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Analyze instruction request metrics over fiscal years, months, or days. Track trends, compare time periods, and identify patterns in your instruction program.
                    </p>
                    <a href="{{ route('statistics.trends') }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        <x-heroicon-o-arrow-right class="w-4 h-4 mr-2" />
                        View Trends Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Export Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <x-heroicon-o-table-cells class="w-8 h-8 text-gray-600 dark:text-gray-300" />
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Detailed Export</h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Coming Soon
                            </span>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Export individual session data with all fields including instructor, course, campus, date, duration, and student count.
                    </p>
                    <button disabled
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-200 dark:bg-gray-700 dark:text-gray-500 rounded-lg cursor-not-allowed">
                        <x-heroicon-o-lock-closed class="w-4 h-4 mr-2" />
                        In Development
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Analysis Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <x-heroicon-o-arrows-right-left class="w-8 h-8 text-gray-600 dark:text-gray-300" />
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Comparison Analysis</h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Coming Soon
                            </span>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Compare metrics side-by-side across multiple campuses or departments. View modality breakdowns and workload distribution for operational planning.
                    </p>
                    <button disabled
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-200 dark:bg-gray-700 dark:text-gray-500 rounded-lg cursor-not-allowed">
                        <x-heroicon-o-lock-closed class="w-4 h-4 mr-2" />
                        In Development
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
