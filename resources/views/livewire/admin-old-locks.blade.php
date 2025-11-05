<div>
    @if($oldLocks->count() > 0)
        <div class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">
                    Old Locks (1+ Hours)
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        {{ $oldLocks->count() }}
                    </span>
                </h4>
                <div class="flex items-center space-x-2">
                    <button
                        wire:click="$refresh"
                        class="inline-flex items-center px-2 py-1 text-xs bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors"
                        title="Refresh list">
                        <x-heroicon-o-arrow-path class="w-3 h-3" />
                    </button>
                    <button
                        wire:click="unlockAllOld"
                        wire:confirm="Unlock ALL old locks? This will affect {{ $oldLocks->count() }} request(s)."
                        class="inline-flex items-center px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                        <x-heroicon-o-lock-open class="w-3 h-3 mr-1" />
                        Unlock All
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Class
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Instructor
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Campus
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Locked By
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Locked
                            </th>
                            <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($oldLocks as $lock)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $lock->classes->course_name ?? ($lock->department . '-' . $lock->course_number) }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $lock->instructor->display_name ?? 'N/A' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs">
                                    @php
                                        $typeColors = [
                                            'on-campus' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            'remote' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                            'asynchronous' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        ];
                                        $colorClass = $typeColors[$lock->instruction_type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                        {{ ucwords(str_replace('-', ' ', $lock->instruction_type)) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $lock->campus->name ?? 'N/A' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $lock->lockedBy->display_name ?? 'Unknown' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <div class="font-medium">{{ $lock->locked_at->format('M j, Y g:i A') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        ({{ $lock->locked_at->diffInHours(now()) }}h ago)
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center">
                                    <button
                                        wire:click="unlockRequest({{ $lock->id }})"
                                        wire:confirm="Unlock this request? The user will lose their editing session."
                                        class="inline-flex items-center px-2 py-1 text-xs bg-orange-600 text-white rounded hover:bg-orange-700 transition-colors">
                                        <x-heroicon-o-lock-open class="w-3 h-3 mr-1" />
                                        Unlock
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="mb-4">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">
                Old Locks (1+ Hours)
            </h4>
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400">No old locks found</span>
                <button
                    wire:click="$refresh"
                    class="inline-flex items-center px-2 py-1 text-xs bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors"
                    title="Refresh list">
                    <x-heroicon-o-arrow-path class="w-3 h-3" />
                </button>
            </div>
        </div>
    @endif
</div>
