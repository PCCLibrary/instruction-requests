@extends('layouts.app')

@section('content')
    <div class="mb-4 p-4 bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-700 rounded-lg">
        <p class="text-yellow-800 dark:text-yellow-200 font-semibold">
            🎨 MOCKUP: Scheduler Dashboard - This is a static preview of the proposed design
        </p>
    </div>

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
            'icon' => 'clipboard-document-list',
            'infoBoxText' => 'Total Active Requests',
            'count' => 156
        ]
    ]"
    />

    {{-- Filters --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800">
            <form class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300 tracking-wider">ACADEMIC YEAR</label>
                    <select class="form-select rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                        <option>2022-2023</option>
                        <option>2023-2024</option>
                        <option selected>2024-2025</option>
                        <option>2025-2026</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300 tracking-wider">CAMPUS</label>
                    <select class="form-select rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                        <option selected>All Campuses</option>
                        <option>Sylvania</option>
                        <option>Rock Creek</option>
                        <option>Cascade</option>
                        <option>Southeast</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Team Workload Table --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between bg-blue-500 dark:bg-blue-700 px-4 py-2">
            <h3 class="text-sm font-medium text-white">Team Workload Distribution</h3>
            <span class="text-xs text-white">AY 2024-2025 • All Campuses</span>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            LIBRARIAN ↕
                        </th>
                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            TOTAL ACTIVE ↕
                        </th>
                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            ASSIGNED ↕
                        </th>
                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            ACCEPTED ↕
                        </th>
                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            IN PROGRESS ↕
                        </th>
                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            SCHEDULED ↕
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>Alice Brown</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Sylvania</div>
                        </td>
                        <td class="px-3 py-2 text-base text-center font-semibold text-gray-900 dark:text-gray-100">18</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">3</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">6</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">2</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">7</td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>Bob Johnson</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Rock Creek</div>
                        </td>
                        <td class="px-3 py-2 text-base text-center font-semibold text-gray-900 dark:text-gray-100">22</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">5</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">8</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">3</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">6</td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>Carol White</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Cascade</div>
                        </td>
                        <td class="px-3 py-2 text-base text-center font-semibold text-gray-900 dark:text-gray-100">15</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">2</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">5</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">4</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">4</td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>David Martinez</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Southeast</div>
                        </td>
                        <td class="px-3 py-2 text-base text-center font-semibold text-gray-900 dark:text-gray-100">12</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">1</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">4</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">1</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">6</td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>Eve Davis</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Sylvania</div>
                        </td>
                        <td class="px-3 py-2 text-base text-center font-semibold text-gray-900 dark:text-gray-100">25</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">7</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">9</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">5</td>
                        <td class="px-3 py-2 text-base text-center text-gray-700 dark:text-gray-300">4</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                            ... and 65 more librarians
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end">
            <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600">
                <x-heroicon-o-arrow-down-tray class="h-4 w-4 mr-2" />
                Export to CSV
            </button>
        </div>
    </div>

    {{-- Needs Attention Table --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between bg-yellow-500 dark:bg-yellow-700 px-4 py-2">
            <h3 class="text-sm font-medium text-white">Needs Attention</h3>
            <span class="text-xs text-white">Priority: Overdue → Stalled → Unassigned</span>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">ISSUE</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">CLASS</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">INSTRUCTOR</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">ASSIGNED TO</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">STATUS</th>
                        <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2">
                            <div class="flex items-center">
                                <span class="text-2xl mr-2">🔴</span>
                                <div>
                                    <div class="text-base font-medium text-red-600 dark:text-red-400">Overdue</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Due 11/20/24</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>BIO 234</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Sylvania</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Amy Johnson</td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Alice Brown</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Accepted
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="#" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <x-heroicon-o-pencil-square class="ml-1 w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2">
                            <div class="flex items-center">
                                <span class="text-2xl mr-2">🔴</span>
                                <div>
                                    <div class="text-base font-medium text-red-600 dark:text-red-400">Overdue</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Due 11/21/24</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>PSY 201</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Rock Creek</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Mark Smith</td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Bob Johnson</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Scheduled
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="#" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <x-heroicon-o-pencil-square class="ml-1 w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2">
                            <div class="flex items-center">
                                <span class="text-2xl mr-2">🟡</span>
                                <div>
                                    <div class="text-base font-medium text-yellow-600 dark:text-yellow-400">Stalled</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">14 days ago</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>HIST 105</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Cascade</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Sarah Lee</td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Carol White</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Accepted
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="#" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <x-heroicon-o-pencil-square class="ml-1 w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2">
                            <div class="flex items-center">
                                <span class="text-2xl mr-2">⚪</span>
                                <div>
                                    <div class="text-base font-medium text-gray-600 dark:text-gray-400">Unassigned</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Received today</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>ENG 104</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Southeast</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">David Brown</td>
                        <td class="px-3 py-2 text-base text-gray-500 dark:text-gray-400 italic">(Unassigned)</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                Received
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="#" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <x-heroicon-o-pencil-square class="ml-1 w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2">
                            <div class="flex items-center">
                                <span class="text-2xl mr-2">⚪</span>
                                <div>
                                    <div class="text-base font-medium text-gray-600 dark:text-gray-400">Unassigned</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">2 days ago</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>MATH 251</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Sylvania</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Lisa Chen</td>
                        <td class="px-3 py-2 text-base text-gray-500 dark:text-gray-400 italic">(Unassigned)</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                Received
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="#" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <x-heroicon-o-pencil-square class="ml-1 w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Calendar Placeholder --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-8 bg-gray-50 dark:bg-gray-800 text-center">
            <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
            <p class="text-base font-medium text-gray-700 dark:text-gray-300">Team Calendar Component</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Month view showing all librarian schedules will appear here</p>
        </div>
    </div>

@endsection
