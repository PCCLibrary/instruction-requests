@extends('layouts.app')

@section('page-title', 'Admin Control Panel')
@section('page-description', 'Manage admin user assignment availability settings.')

@section('content')
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">

            <!-- Single column full-width layout -->
            <div class="space-y-8">

                <!-- Test Notifications Section -->
                @livewire('admin-test-notifications')

                <!-- Mail Queue & Notifications Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Mail Queue & Notifications</h3>

                    <!-- Unified Statistics Display (no border, no secondary title) -->
                    <div class="mb-6">
                        <!-- Email Queue Status Row -->
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Email Queue</span>
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

                        <!-- 24-Hour Summary Row -->
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Last 24 Hours</span>
                            <div class="flex items-center space-x-4" id="notification-summary">
                                <div class="flex items-center space-x-2">
                                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 dark:text-green-400" />
                                    <span class="text-sm text-gray-900 dark:text-gray-100">
                                        <span id="notifications-sent">0</span> sent (<span id="notifications-sent-percent">0</span>%)
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <x-heroicon-o-exclamation-circle class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    <span class="text-sm text-gray-900 dark:text-gray-100">
                                        <span id="notifications-failed">0</span> failed (<span id="notifications-failed-percent">0</span>%)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Last Sent Row -->
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Last Sent</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100" id="last-sent">Never</span>
                        </div>

                        <!-- Breakdown Grid -->
                        <div class="grid grid-cols-2 gap-6">
                            <!-- By Recipient -->
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">By Recipient</div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Instructors</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100" id="recipient-instructors">0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Librarians</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100" id="recipient-librarians">0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Schedulers</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100" id="recipient-schedulers">0</span>
                                    </div>
                                </div>
                            </div>

                            <!-- By Type -->
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">By Type</div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-blue-600 dark:text-blue-400">Received</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100" id="type-received">0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-amber-600 dark:text-amber-400">Assigned</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100" id="type-assigned">0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-green-600 dark:text-green-400">Accepted</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100" id="type-accepted">0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-red-600 dark:text-red-400">Rejected</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100" id="type-rejected">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Queue Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
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

                    <!-- Collapsible Recent Notifications Table -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                        <button id="toggle-notifications-table"
                                class="flex items-center justify-between w-full text-left mb-4 hover:bg-gray-50 dark:hover:bg-gray-700 p-2 rounded transition-colors">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                Recent Notifications (<span id="notifications-total">0</span>)
                            </span>
                            <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400" id="chevron-icon" />
                        </button>

                        <div id="notifications-table-container" class="hidden space-y-4">
                            <!-- Table Controls -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Show:</label>
                                    <select id="notification-row-count"
                                            class="text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded px-2 py-1">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="overflow-x-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Type
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Recipient
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Request
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Time
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="notifications-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                Loading notifications...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Table Footer -->
                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span id="notifications-showing">Showing 0 of 0 notifications</span>
                            </div>
                        </div>
                    </div>

                    <!-- Help Text -->
                    <div class="mt-6 text-xs text-gray-600 dark:text-gray-400 border-t border-gray-200 dark:border-gray-600 pt-4">
                        <p>System monitoring with automatic 30-second refresh. Tracks email queue status and notification sending failures for the last 24 hours. Note: Only SMTP/sending failures are tracked; email bounces and delivery confirmations are not monitored.</p>
                    </div>
                </div>

                <!-- Application Cache Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Application Cache</h3>
                    <form method="POST" action="{{ route('admin.clear-cache') }}" class="inline-block w-full mb-2">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Clear all application caches? This will temporarily slow down the next few requests.')"
                                class="w-full md:w-auto flex items-center justify-center space-x-2 px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <x-heroicon-o-trash class="w-4 h-4" />
                            <span>Clear Application Cache</span>
                        </button>
                    </form>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        <p>Clears application, config, route, and view caches to resolve configuration issues.</p>
                    </div>
                </div>

                <!-- Admin User Assignment Availability Section -->
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

                <!-- Active Locks Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Active Edit Locks</h3>
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

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let notificationsTableExpanded = false;

    // Toggle notifications table
    const toggleButton = document.getElementById('toggle-notifications-table');
    const tableContainer = document.getElementById('notifications-table-container');
    const chevronIcon = document.getElementById('chevron-icon');

    if (toggleButton && tableContainer) {
        toggleButton.addEventListener('click', function() {
            notificationsTableExpanded = !notificationsTableExpanded;

            if (notificationsTableExpanded) {
                tableContainer.classList.remove('hidden');
                chevronIcon.classList.add('rotate-180');
                loadRecentNotifications();
            } else {
                tableContainer.classList.add('hidden');
                chevronIcon.classList.remove('rotate-180');
            }
        });
    }

    // Row count change handler
    const rowCountSelect = document.getElementById('notification-row-count');
    if (rowCountSelect) {
        rowCountSelect.addEventListener('change', function() {
            if (notificationsTableExpanded) {
                loadRecentNotifications();
            }
        });
    }

    // Format timestamp as relative time
    function formatRelativeTime(timestamp) {
        if (!timestamp) return 'Never';

        const now = new Date();
        const then = new Date(timestamp);
        const diffMs = now - then;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins === 1) return '1 minute ago';
        if (diffMins < 60) return `${diffMins} minutes ago`;
        if (diffHours === 1) return '1 hour ago';
        if (diffHours < 24) return `${diffHours} hours ago`;
        if (diffDays === 1) return '1 day ago';
        return `${diffDays} days ago`;
    }

    // Load recent notifications (placeholder - will need backend endpoint)
    function loadRecentNotifications() {
        const limit = document.getElementById('notification-row-count').value;
        const tbody = document.getElementById('notifications-table-body');

        if (!tbody) return;

        // Placeholder: Show loading state
        tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Loading notifications...</td></tr>';

        // TODO: Fetch from backend endpoint
        // For now, just show "no data" message
        setTimeout(() => {
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No recent notifications available.</td></tr>';
        }, 500);
    }

    // Update notification statistics
    function updateNotificationStats(data) {
        if (!data.notifications) return;

        const notifications = data.notifications;
        const total = notifications.total || 0;
        const sent = notifications.sent || 0;
        const failed = notifications.failed || 0;

        // Update counts
        document.getElementById('notifications-sent').textContent = sent;
        document.getElementById('notifications-failed').textContent = failed;
        document.getElementById('notifications-total').textContent = total;

        // Update percentages
        const sentPercent = total > 0 ? ((sent / total) * 100).toFixed(1) : '0';
        const failedPercent = total > 0 ? ((failed / total) * 100).toFixed(1) : '0';
        document.getElementById('notifications-sent-percent').textContent = sentPercent;
        document.getElementById('notifications-failed-percent').textContent = failedPercent;

        // Update last sent
        const lastSent = notifications.last_sent ? formatRelativeTime(notifications.last_sent) : 'Never';
        document.getElementById('last-sent').textContent = lastSent;

        // Update by recipient
        if (notifications.by_recipient) {
            document.getElementById('recipient-instructors').textContent = notifications.by_recipient.instructors || 0;
            document.getElementById('recipient-librarians').textContent = notifications.by_recipient.librarians || 0;
            document.getElementById('recipient-schedulers').textContent = notifications.by_recipient.schedulers || 0;
        }

        // Update by type
        if (notifications.by_type) {
            document.getElementById('type-received').textContent = notifications.by_type.received || 0;
            document.getElementById('type-assigned').textContent = notifications.by_type.assigned || 0;
            document.getElementById('type-accepted').textContent = notifications.by_type.accepted || 0;
            document.getElementById('type-rejected').textContent = notifications.by_type.rejected || 0;
        }
    }

    // System status refresh function
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

            // Update notification statistics
            updateNotificationStats(data);

            // Refresh Livewire old locks component
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('refreshOldLocks');
            }
        })
        .catch(error => {
            console.log('Status update failed:', error);
        });
    }

    // Update every 30 seconds
    setInterval(updateSystemStatus, 30000);

    // Initial update after 5 seconds to let page settle
    setTimeout(updateSystemStatus, 5000);
});
</script>
@endpush
