@extends('layouts.app')

@section('content')
    {{-- Status Bar --}}
    <x-status-bar
        :items="[
        [
            'iconBgColor' => 'bg-amber-500 dark:bg-amber-700',
            'icon' => 'clock',
            'infoBoxText' => 'Pending Your Response',
            'count' => 12
        ],
        [
            'iconBgColor' => 'bg-blue-500 dark:bg-blue-700',
            'icon' => 'clipboard-list',
            'infoBoxText' => 'My Active Requests',
            'count' => 8
        ],
        [
            'iconBgColor' => 'bg-green-500 dark:bg-green-700',
            'icon' => 'check-circle',
            'infoBoxText' => 'Completed This Term',
            'count' => 45
        ]
    ]"
    />

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
        {{-- Left Column: Pending Response --}}
        <div class="col-span-12 xl:col-span-5">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-amber-500 dark:bg-amber-700">
                    <h2 class="text-lg font-semibold text-white">Pending Your Response</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">BIO 101 - Jane Smith</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Rock Creek | Oct 15, 2024</div>
                        </div>
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">ENG 104 - Robert Lee</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Cascade | Oct 14, 2024</div>
                        </div>
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">MATH 251 - Lisa Chen</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Sylvania | Oct 13, 2024</div>
                        </div>
                        <div class="text-center text-sm text-gray-500 dark:text-gray-400 pt-2">
                            ... and 9 more
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Recently Received --}}
        <div class="col-span-12 xl:col-span-7">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-blue-500 dark:bg-blue-700">
                    <h2 class="text-lg font-semibold text-white">Recently Received</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">PSY 201 - Mark Johnson</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Sylvania | Status: Received</div>
                        </div>
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">HIST 105 - Sarah Williams</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Rock Creek | Status: Received</div>
                        </div>
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">CS 161 - David Martinez</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Southeast | Status: Received</div>
                        </div>
                        <div class="text-center">
                            <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                View All Received (25) →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- My Active Requests Table --}}
    <div class="mt-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-blue-500 dark:bg-blue-700">
                <h2 class="text-lg font-semibold text-white">My Active Requests</h2>
                <p class="text-sm text-blue-100 dark:text-blue-200">Sorted by due date (soonest first)</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Urgency
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Class
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Instructor
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Due Date
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- Overdue Request --}}
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-2xl">🔴</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-semibold">BIO 234</div>
                                <div class="text-sm text-gray-500">Sylvania</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Amy Johnson</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Synchronous
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Nov 20, 2024</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Accepted
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Click to Schedule
                                </button>
                            </td>
                        </tr>

                        {{-- Due Soon - Async --}}
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-2xl">🟡</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-semibold">PSY 101 📝</div>
                                <div class="text-sm text-gray-500">Rock Creek</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Bob Smith</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    Asynchronous
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Nov 25, 2024</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Accepted
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                    Mark In Progress
                                </button>
                            </td>
                        </tr>

                        {{-- Scheduled --}}
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-2xl">🟡</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-semibold">HIST 202</div>
                                <div class="text-sm text-gray-500">Cascade</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Carol White</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Synchronous
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Nov 27, 2024</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Scheduled
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Mark Complete
                                </button>
                            </td>
                        </tr>

                        {{-- In Progress --}}
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-2xl">⚪</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-semibold">ENG 104 📝</div>
                                <div class="text-sm text-gray-500">Southeast</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Dan Brown</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    Asynchronous
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Dec 5, 2024</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                    In Progress
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Mark Complete
                                </button>
                            </td>
                        </tr>

                        {{-- Upcoming --}}
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-2xl">⚪</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-semibold">MATH 111</div>
                                <div class="text-sm text-gray-500">Sylvania</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Eve Davis</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Synchronous
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">Dec 10, 2024</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Scheduled
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Mark Complete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <div class="flex gap-6 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-1">
                        <span class="text-xl">🔴</span>
                        <span>Overdue</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-xl">🟡</span>
                        <span>Due Soon (7 days)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-xl">⚪</span>
                        <span>Upcoming</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-xl">📝</span>
                        <span>Async Request</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Placeholder for Calendar Component --}}
    <div class="mt-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
            <div class="text-gray-400 dark:text-gray-500">
                <i class="fa fa-calendar text-6xl mb-4"></i>
                <p class="text-lg font-semibold">Calendar Component (Future Implementation)</p>
                <p class="text-sm mt-2">Month grid view with request status indicators will appear here</p>
            </div>
        </div>
    </div>
@endsection
