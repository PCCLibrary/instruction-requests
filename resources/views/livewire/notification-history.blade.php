<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Notification History
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ count($this->notificationLogs) }} sent
            </span>
        </div>
    </div>

    @if(count($this->notificationLogs) > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Recipients</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->notificationLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColors = [
                                        'received' => 'bg-blue-500',
                                        'assigned' => 'bg-amber-500',
                                        'accepted' => 'bg-teal-500',
                                        'rejected' => 'bg-rose-500'
                                    ];
                                    $color = $badgeColors[$log->notification_type] ?? 'bg-gray-500';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white {{ $color }}">
                                    {{ ucfirst($log->notification_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->is_group)
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $log->campus_name }} Schedulers</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                {{ $log->count }} sent
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $log->recipient_email }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                {{ ucfirst($log->recipient_type) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($log->sent_at)->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($log->sent_at)->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($log->is_group)
                                    <button
                                        wire:click="resendGroup({{ json_encode($log->log_ids) }})"
                                        wire:loading.attr="disabled"
                                        wire:target="resendGroup({{ json_encode($log->log_ids) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 dark:bg-gray-600 hover:bg-gray-800 dark:hover:bg-gray-500 text-white text-xs font-medium rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span wire:loading.remove wire:target="resendGroup({{ json_encode($log->log_ids) }})">Resend All</span>
                                        <span wire:loading wire:target="resendGroup({{ json_encode($log->log_ids) }})">Sending...</span>
                                    </button>
                                @else
                                    <button
                                        wire:click="resendNotification({{ $log->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="resendNotification({{ $log->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 dark:bg-gray-600 hover:bg-gray-800 dark:hover:bg-gray-500 text-white text-xs font-medium rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span wire:loading.remove wire:target="resendNotification({{ $log->id }})">Resend</span>
                                        <span wire:loading wire:target="resendNotification({{ $log->id }})">Sending...</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
            No notifications have been sent for this request yet.
        </div>
    @endif
</div>
