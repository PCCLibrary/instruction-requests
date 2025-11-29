@extends('layouts.app')

@section('content')
    <div class="mb-4 p-4 bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-700 rounded-lg">
        <p class="text-yellow-800 dark:text-yellow-200 font-semibold">
            🎨 MOCKUP: Librarian Dashboard - This is a static preview of the proposed design
        </p>
    </div>

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
            'icon' => 'clipboard-document-list',
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

    {{-- Two-column layout matching existing dashboard --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 mt-4">
        {{-- Left Column: Pending Response --}}
        {{-- IMPLEMENTATION NOTE: Shows requests in "assigned" status waiting for librarian to accept/reject.
             Edit button links to full request edit page where they can accept or reject.
        --}}
        <div class="col-span-12 xl:col-span-5">
            <div class="w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between bg-amber-500 dark:bg-amber-700 px-4 py-2">
                    <h3 class="text-sm font-medium text-white">Pending Your Response</h3>
                    <span class="text-xs text-white">10 most recent</span>
                </div>

                <div class="w-full overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Date</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Campus</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Class</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Instructor</th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">10/15/24</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">Rock Creek</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">BIO 101</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">Jane Smith</td>
                                <td class="px-3 py-2 text-right">
                                    <a href="#" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                </td>
                            </tr>
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">10/14/24</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">Cascade</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">ENG 104</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">Robert Lee</td>
                                <td class="px-3 py-2 text-right">
                                    <a href="#" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                </td>
                            </tr>
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">10/13/24</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">Sylvania</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">MATH 251</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">Lisa Chen</td>
                                <td class="px-3 py-2 text-right">
                                    <a href="#" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                    ... and 7 more requests
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Recently Received --}}
        {{-- IMPLEMENTATION NOTE: Shows all recently received requests regardless of assignment.
             Helps librarians see what's coming in and proactively claim requests.
             Edit button links to full request edit page.
        --}}
        <div class="col-span-12 xl:col-span-7">
            <div class="w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between bg-blue-500 dark:bg-blue-700 px-4 py-2">
                    <h3 class="text-sm font-medium text-white">Recently Received</h3>
                </div>

                <div class="w-full overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Received</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Campus</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Class</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Instructor</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Status</th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-3 py-2 text-base text-gray-500 dark:text-gray-400">10/28/24 2:48 PM</td>
                                <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Sylvania</td>
                                <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">PSY 201</td>
                                <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Mark Johnson</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
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
                                <td class="px-3 py-2 text-base text-gray-500 dark:text-gray-400">10/07/24 8:40 AM</td>
                                <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Rock Creek</td>
                                <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">HIST 105</td>
                                <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Sarah Williams</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
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
                                <td class="px-3 py-2 text-base text-gray-500 dark:text-gray-400">07/28/24 12:33 PM</td>
                                <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Southeast</td>
                                <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">CS 161</td>
                                <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">David Martinez</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
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

                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All Received Requests (25) →</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Full-width Active Requests Table - PowerGrid style --}}
    {{-- IMPLEMENTATION NOTE: This table will show all active requests (assigned, accepted, scheduled, in_progress)
         sorted by due date with urgency indicators. Action buttons vary by status and type:
         - Accepted + Sync: "Schedule" button (opens CreateGoogleCalendarEventForm modal)
         - Accepted + Async: "Mark In Progress" button (simple status update)
         - Scheduled or In Progress: "Mark Complete" button (status update to completed)
         The Google Calendar modal component is already built and only needs the request ID.
    --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between bg-blue-500 dark:bg-blue-700 px-4 py-2">
            <h3 class="text-sm font-medium text-white">My Active Requests</h3>
            <span class="text-xs text-white">Sorted by due date (soonest first)</span>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">⚠️</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Class</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Instructor</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Type</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Due Date</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Status</th>
                        <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    {{-- Example: Overdue accepted sync request - shows Schedule button --}}
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-center text-2xl">🔴</td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>BIO 234</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Sylvania</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Amy Johnson</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Sync</span>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Nov 20, 2024</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Accepted</span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="#" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                                <button class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded">
                                    <x-heroicon-o-calendar-date-range class="h-3 w-3 mr-1" />
                                    Schedule
                                </button>
                            </div>
                        </td>
                    </tr>
                    {{-- Example: Due soon accepted async request - shows Mark In Progress button --}}
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-center text-2xl">🟡</td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>PSY 101 📝</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Rock Creek</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Bob Smith</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Async</span>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Nov 25, 2024</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Accepted</span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="#" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                                <button class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 rounded">
                                    Mark In Progress
                                </button>
                            </div>
                        </td>
                    </tr>
                    {{-- Example: Scheduled sync request - shows Mark Complete button --}}
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-center text-2xl">🟡</td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>HIST 202</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Cascade</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Carol White</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Sync</span>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Nov 27, 2024</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Scheduled</span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="#" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                                <button class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded">
                                    <x-heroicon-o-check-circle class="h-3 w-3 mr-1" />
                                    Mark Complete
                                </button>
                            </div>
                        </td>
                    </tr>
                    {{-- Example: In-progress async request - shows Mark Complete button --}}
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-center text-2xl">⚪</td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>ENG 104 📝</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Southeast</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Dan Brown</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Async</span>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Dec 5, 2024</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">In Progress</span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="#" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                                <button class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded">
                                    <x-heroicon-o-check-circle class="h-3 w-3 mr-1" />
                                    Mark Complete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 py-2 text-center text-2xl">⚪</td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">
                            <div>MATH 111</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Sylvania</div>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Eve Davis</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Sync</span>
                        </td>
                        <td class="px-3 py-2 text-base text-gray-900 dark:text-gray-100">Dec 10, 2024</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Scheduled</span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="#" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                                <button class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded">
                                    <x-heroicon-o-check-circle class="h-3 w-3 mr-1" />
                                    Mark Complete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <strong>Legend:</strong> 🔴 = Overdue &nbsp;&nbsp; 🟡 = Due Soon (7 days) &nbsp;&nbsp; ⚪ = Upcoming &nbsp;&nbsp; 📝 = Async Request
            </div>
        </div>
    </div>

    {{-- Calendar Placeholder --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-8 bg-gray-50 dark:bg-gray-800 text-center">
            <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
            <p class="text-base font-medium text-gray-700 dark:text-gray-300">Calendar Component</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Month view showing scheduled sessions will appear here</p>
        </div>
    </div>

@endsection
