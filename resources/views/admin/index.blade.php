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

            <!-- Two-column grid layout -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

                <!-- Left Column: Existing Admin Users Table -->
                <div>
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

                    <!-- Explanation text at bottom without grey box -->
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                        <p><strong>Available:</strong> Admin appears in librarian selection dropdowns for assignment and testing purposes.</p>
                        <p><strong>Hidden:</strong> Admin is suppressed from librarian selection lists (normal state for admin users).</p>
                        <p><strong>Note:</strong> Changes take effect immediately across all system components.</p>
                    </div>
                </div>

                <!-- Right Column: System Management -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        System Management
                    </h3>

                    <!-- Mail Queue Section -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Mail Queue</h4>

                        <!-- Queue Status Display -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg mb-3">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Email Queue Status</span>
                            <span id="queue-badge">
                                @php
                                    $queuePending = DB::table('jobs')->count();
                                    $queueFailed = DB::table('failed_jobs')->count();
                                @endphp
                                @if($queueFailed > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        ⚠️ {{ $queueFailed }} failed{{ $queuePending > 0 ? ", {$queuePending} pending" : "" }}
                                    </span>
                                @elseif($queuePending > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        ⏳ {{ $queuePending }} pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        ✅ Queue empty
                                    </span>
                                @endif
                            </span>
                        </div>

                        <!-- Queue Actions -->
                        <div class="space-y-3 mb-2">
                            <form method="POST" action="{{ route('admin.flush-queue') }}" class="inline-block w-full">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Remove all pending jobs from the queue? This cannot be undone.')"
                                        class="w-full flex items-center justify-center space-x-2 px-4 py-2 text-sm bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                                    <x-heroicon-o-stop class="w-4 h-4" />
                                    <span>Flush Mail Queue</span>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.restart-queue') }}" class="inline-block w-full">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Restart queue workers? Current jobs will finish, then workers will restart.')"
                                        class="w-full flex items-center justify-center space-x-2 px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                                    <span>Restart Queue Workers</span>
                                </button>
                            </form>
                        </div>

                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            <p>Shows pending and failed email jobs (updates every 30 seconds). Manage email delivery system with flush and restart actions.</p>
                        </div>
                    </div>

                    <!-- Active Locks Section -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Active Edit Locks</h4>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg mb-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Edit Sessions</span>
                            <span id="locks-badge">
                                @php
                                    $activeLocks = \App\Models\InstructionRequests::where('locked', true)->count();
                                @endphp
                                @if($activeLocks > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        🔒 {{ $activeLocks }} locked
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        ✅ No locks
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-4">
                            <p>Shows instruction requests currently being edited by users.</p>
                        </div>

                        <!-- Old Locks Management Component -->
                        <livewire:admin-old-locks />
                    </div>

                    <!-- Application Cache Section -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Application Cache</h4>
                        <form method="POST" action="{{ route('admin.clear-cache') }}" class="inline-block w-full mb-2">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Clear all application caches? This will temporarily slow down the next few requests.')"
                                    class="w-full flex items-center justify-center space-x-2 px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <x-heroicon-o-trash class="w-4 h-4" />
                                <span>Clear Application Cache</span>
                            </button>
                        </form>
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            <p>Clears application, config, route, and view caches to resolve configuration issues.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple status refresh function following app patterns
    function updateSystemStatus() {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route('admin.system-status') }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const queueBadge = document.getElementById('queue-badge');
            const locksBadge = document.getElementById('locks-badge');

            if (queueBadge && data.queue_html) {
                queueBadge.innerHTML = data.queue_html;
            }
            if (locksBadge && data.locks_html) {
                locksBadge.innerHTML = data.locks_html;
            }

            // Refresh Livewire old locks component
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('refreshOldLocks');
            }
        })
        .catch(error => {
            console.log('Status update failed:', error);
            // Don't show user notifications for status update failures
        });
    }

    // Update every 30 seconds
    setInterval(updateSystemStatus, 30000);

    // Initial update after 5 seconds to let page settle
    setTimeout(updateSystemStatus, 5000);
});
</script>
@endpush
