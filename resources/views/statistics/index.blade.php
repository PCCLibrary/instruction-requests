@extends('layouts.app')

@section('content')
<div x-data="{ filtersExpanded: true, viewBy: 'year' }">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Statistics Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-300">Instruction request metrics and reporting</p>
    </div>

    <!-- Filter Panel -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <!-- Filter Header -->
        <div class="bg-purple-500 dark:bg-purple-600 px-4 py-3 rounded-t-lg flex items-center justify-between">
            <h3 class="text-lg font-medium text-white">Report Filters</h3>
            <button @click="filtersExpanded = !filtersExpanded" class="text-white hover:text-gray-200">
                <svg x-show="!filtersExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                <svg x-show="filtersExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
            </button>
        </div>

        <!-- Filter Content -->
        <div x-show="filtersExpanded" x-collapse class="p-4">
            <!-- Primary Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <!-- View By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        View By
                    </label>
                    <div class="flex gap-2">
                        <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer" :class="viewBy === 'year' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <input type="radio" name="viewBy" value="year" x-model="viewBy" class="mr-2">
                            <span class="text-sm dark:text-white">Year</span>
                        </label>
                        <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer" :class="viewBy === 'month' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <input type="radio" name="viewBy" value="month" x-model="viewBy" class="mr-2">
                            <span class="text-sm dark:text-white">Month</span>
                        </label>
                        <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer" :class="viewBy === 'day' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <input type="radio" name="viewBy" value="day" x-model="viewBy" class="mr-2">
                            <span class="text-sm dark:text-white">Day</span>
                        </label>
                    </div>
                </div>

                <!-- Date Range - Changes based on View By -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span x-show="viewBy === 'year'">Fiscal Year Range</span>
                        <span x-show="viewBy === 'month'">Month Range</span>
                        <span x-show="viewBy === 'day'">Date Range</span>
                    </label>

                    <!-- Year View: Fiscal Year Selectors -->
                    <div x-show="viewBy === 'year'" class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Start Fiscal Year</label>
                            <select class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                <option>2020-2021</option>
                                <option>2021-2022</option>
                                <option>2022-2023</option>
                                <option>2023-2024</option>
                                <option selected>2024-2025</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">End Fiscal Year</label>
                            <select class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                <option>2020-2021</option>
                                <option>2021-2022</option>
                                <option>2022-2023</option>
                                <option>2023-2024</option>
                                <option selected>2024-2025</option>
                            </select>
                        </div>
                    </div>

                    <!-- Month View: Month Pickers -->
                    <div x-show="viewBy === 'month'" class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Start Month</label>
                            <input type="month" value="2024-07" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">End Month</label>
                            <input type="month" value="2025-06" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Day View: Date Pickers -->
                    <div x-show="viewBy === 'day'" class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Start Date</label>
                            <input type="date" value="2024-10-01" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">End Date</label>
                            <input type="date" value="2024-10-31" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Campus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Campus
                    </label>
                    <select class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option>All Campuses</option>
                        <option>Cascade</option>
                        <option>Rock Creek</option>
                        <option>Southeast</option>
                        <option>Sylvania</option>
                    </select>
                </div>
                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Department
                    </label>
                    <select class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option>All Departments</option>
                        <option>ENG - English</option>
                        <option>BI - Biology</option>
                        <option>MTH - Math</option>
                        <option>PSY - Psychology</option>
                        <option>HST - History</option>
                    </select>
                </div>

                <!-- Instructor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Instructor
                    </label>
                    <select class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option>All Instructors</option>
                        <option>Anderson, Prof.</option>
                        <option>Chen, Dr.</option>
                        <option>Johnson, Prof.</option>
                        <option>Martinez, Dr.</option>
                    </select>
                </div>

                <!-- Assigned Librarian -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Assigned Librarian
                    </label>
                    <select class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option>All Librarians</option>
                        <option>Sarah Miller</option>
                        <option>David Chen</option>
                        <option>Lisa Rodriguez</option>
                        <option>Michael Torres</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Apply Filters
                </button>
                <button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Clear All Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    <div class="mb-4 flex flex-wrap gap-2">
        <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-3 py-1 rounded-full text-sm">
            Campus: Sylvania
            <button class="hover:text-blue-600 dark:hover:text-blue-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </span>
        <span class="inline-flex items-center gap-2 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 px-3 py-1 rounded-full text-sm">
            Department: ENG - English
            <button class="hover:text-purple-600 dark:hover:text-purple-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </span>
    </div>

    <!-- Statistics Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-green-500 dark:bg-green-600 px-4 py-3 flex items-center justify-between">
            <h3 class="text-lg font-medium text-white">Instruction Type Report</h3>
            <div class="flex gap-2">
                <button class="bg-white hover:bg-gray-100 text-green-700 px-3 py-1 rounded-md text-sm font-medium">
                    Export CSV
                </button>
                <button class="bg-white hover:bg-gray-100 text-green-700 px-3 py-1 rounded-md text-sm font-medium">
                    Export Excel
                </button>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-700">
                            Category
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            2024-2025
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            2023-2024
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            2022-2023
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            2021-2022
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            2020-2021
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Synchronous -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800">
                            Synchronous
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">245</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">218</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">192</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">203</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">156</span>
                        </td>
                    </tr>

                    <!-- Asynchronous -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800">
                            Asynchronous
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">87</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">95</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">102</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">78</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">124</span>
                        </td>
                    </tr>

                    <!-- Attendance -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 bg-blue-50 dark:bg-blue-900/20">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white sticky left-0 bg-blue-50 dark:bg-blue-900/20">
                            Attendance (Students)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold text-blue-700 dark:text-blue-400">7,834</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold text-blue-700 dark:text-blue-400">7,142</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold text-blue-700 dark:text-blue-400">6,823</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold text-blue-700 dark:text-blue-400">6,455</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold text-blue-700 dark:text-blue-400">5,987</span>
                        </td>
                    </tr>

                    <!-- Remote -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800">
                            Remote
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">156</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">142</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">128</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">134</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">98</span>
                        </td>
                    </tr>

                    <!-- In Person -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800">
                            In Person
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">89</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">76</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">64</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">69</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">58</span>
                        </td>
                    </tr>

                    <!-- ADA -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800">
                            ADA
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">12</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">15</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">18</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">11</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">9</span>
                        </td>
                    </tr>

                    <!-- No ADA -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800">
                            No ADA
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">320</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">298</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">276</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">270</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                            <span class="font-semibold">271</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer with Record Count -->
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-t border-gray-200 dark:border-gray-600">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Showing 7 categories across 5 fiscal years.
                <span class="font-medium">Total requests: 332</span> for selected period.
            </p>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Sessions</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">332</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Students Reached</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">7,834</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">YoY Growth</p>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">+12.4%</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-amber-500 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg per Month</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">28</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
