@extends('layouts.app')

@section('content')
    {{-- Status Bar --}}
    <x-status-bar
        :items="[
        [
            'iconBgColor' => 'bg-red-500 dark:bg-red-700',
            'icon' => 'inbox',
            'infoBoxText' => 'Unassigned Requests',
            'count' => 15
        ],
        [
            'iconBgColor' => 'bg-yellow-500 dark:bg-yellow-700',
            'icon' => 'exclamation-triangle',
            'infoBoxText' => 'Needs Attention',
            'count' => 23
        ],
        [
            'iconBgColor' => 'bg-blue-500 dark:bg-blue-700',
            'icon' => 'clipboard-list',
            'infoBoxText' => 'Total Active Requests',
            'count' => 156
        ]
    ]"
    />

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
        <div class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Fiscal Year</label>
                <select class="form-select rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option>2022-2023</option>
                    <option>2023-2024</option>
                    <option selected>2024-2025</option>
                    <option>2025-2026</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Campus</label>
                <select class="form-select rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option selected>All Campuses</option>
                    <option>Sylvania</option>
                    <option>Rock Creek</option>
                    <option>Cascade</option>
                    <option>Southeast</option>
                </select>
            </div>
            <div>
                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    {{-- Team Workload Distribution --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold">Team Workload Distribution</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Click column headers to sort</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                            Librarian ↕️
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                            Total Active ↕️
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                            Assigned ↕️
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                            Accepted ↕️
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                            In Progress ↕️
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                            Scheduled ↕️
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">Alice Brown</div>
                            <div class="text-sm text-gray-500">Sylvania</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center font-semibold">18</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">6</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">7</td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">Bob Johnson</div>
                            <div class="text-sm text-gray-500">Rock Creek</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center font-semibold">22</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">5</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">8</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">6</td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">Carol White</div>
                            <div class="text-sm text-gray-500">Cascade</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center font-semibold">15</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">5</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">4</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">4</td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">David Martinez</div>
                            <div class="text-sm text-gray-500">Southeast</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center font-semibold">12</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">4</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">6</td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">Eve Davis</div>
                            <div class="text-sm text-gray-500">Sylvania</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center font-semibold">25</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">7</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">9</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">5</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">4</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            ... and 65 more librarians
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-end">
            <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                <i class="fa fa-download mr-2"></i>
                Export to CSV
            </button>
        </div>
    </div>

    {{-- Needs Attention --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold">Needs Attention</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Sorted by priority: Overdue → Stalled → Unassigned</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Issue
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Class
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Instructor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Assigned To
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {{-- Overdue --}}
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-red-600 dark:text-red-400 font-semibold">🔴 OVERDUE</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">BIO 234</div>
                            <div class="text-sm text-gray-500">Sylvania</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Amy Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap">Alice Brown</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Accepted
                            </span>
                            <div class="text-xs text-gray-500 mt-1">11/20/24</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                [Edit]
                            </a>
                        </td>
                    </tr>

                    {{-- Another Overdue --}}
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-red-600 dark:text-red-400 font-semibold">🔴 OVERDUE</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">PSY 201</div>
                            <div class="text-sm text-gray-500">Rock Creek</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Mark Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap">Bob Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Scheduled
                            </span>
                            <div class="text-xs text-gray-500 mt-1">11/21/24</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                [Edit]
                            </a>
                        </td>
                    </tr>

                    {{-- Stalled --}}
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-yellow-600 dark:text-yellow-400 font-semibold">🟡 STALLED</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">HIST 105</div>
                            <div class="text-sm text-gray-500">Cascade</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Sarah Lee</td>
                        <td class="px-6 py-4 whitespace-nowrap">Carol White</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Accepted
                            </span>
                            <div class="text-xs text-gray-500 mt-1">14 days ago</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                [Edit]
                            </a>
                        </td>
                    </tr>

                    {{-- Unassigned --}}
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-600 dark:text-gray-400">⚪ UNASSIGNED</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">ENG 104</div>
                            <div class="text-sm text-gray-500">Southeast</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">David Brown</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-400">(Unassigned)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                Received
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                [Edit]
                            </a>
                        </td>
                    </tr>

                    {{-- Another Unassigned --}}
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-600 dark:text-gray-400">⚪ UNASSIGNED</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">MATH 251</div>
                            <div class="text-sm text-gray-500">Sylvania</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Lisa Chen</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-400">(Unassigned)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                Received
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                [Edit]
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Placeholder for Calendar Component --}}
    <div class="mt-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
            <div class="text-gray-400 dark:text-gray-500">
                <i class="fa fa-calendar text-6xl mb-4"></i>
                <p class="text-lg font-semibold">Calendar Component (Future Implementation)</p>
                <p class="text-sm mt-2">Month grid view with all librarian requests will appear here</p>
            </div>
        </div>
    </div>
@endsection
