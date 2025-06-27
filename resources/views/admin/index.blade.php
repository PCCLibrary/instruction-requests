@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Admin Control Panel']
        ]" />
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Admin Control Panel</h1>
        <p class="mt-1 text-gray-500 dark:text-gray-300">Manage admin user assignment availability settings.</p>
    </div>
@endsection

@section('content')
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">

            <!-- Admin Users Table -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Admin User Assignment Availability
                </h3>

                @if($adminUsers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Admin User
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Assignment Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($adminUsers as $adminUser)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $adminUser->display_name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $adminUser->email }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($adminUser->available_for_assignment)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Available
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Hidden
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <form method="POST" action="{{ route('admin.toggle-assignment') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $adminUser->id }}">
                                                <button type="submit"
                                                        class="px-3 py-1.5 text-xs font-medium rounded-md text-white transition-colors
                                                               @if($adminUser->available_for_assignment)
                                                                   bg-red-600 hover:bg-red-700
                                                               @else
                                                                   bg-green-600 hover:bg-green-700
                                                               @endif">
                                                    @if($adminUser->available_for_assignment)
                                                        Disable
                                                    @else
                                                        Enable
                                                    @endif
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">No admin users found.</p>
                    </div>
                @endif
            </div>

            <!-- Info Section -->
            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">About Assignment Availability</h4>
                <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                    <p><strong>Available:</strong> Admin appears in librarian selection dropdowns for assignment and testing purposes.</p>
                    <p><strong>Hidden:</strong> Admin is suppressed from librarian selection lists (normal state for admin users).</p>
                    <p><strong>Note:</strong> Changes take effect immediately across all system components.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
