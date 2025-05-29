<div>
    <!-- Save/Cancel Controls -->
    @if($hasChanges)
    <div class="mb-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-amber-800 dark:text-amber-200 font-medium">Campus order has been changed.</span>
                <span class="text-amber-700 dark:text-amber-300 ml-1">Click Save to make it permanent or Cancel to revert.</span>
            </div>
            <div class="flex space-x-2">
                <button wire:click="saveOrder" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">Save</button>
                <button wire:click="cancelOrder" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Campus Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10"></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Campus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notifications to</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">GCal Key</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="sortable-table" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($campuses as $campus)
                <tr data-id="{{ $campus->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="drag-handle text-gray-400 hover:text-gray-600 cursor-grab">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM7 8a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM7 14a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM13 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM13 8a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM13 14a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/>
                            </svg>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $campus->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $campus->code }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                        @if($campus->librarian_ids)
                            @php
                                $librarians = \App\Models\User::whereIn('id', $campus->librarian_ids)->pluck('display_name');
                            @endphp
                            <div class="flex flex-wrap gap-1 max-w-m">
                                @foreach($librarians as $librarian)
                                    <span class="inline-flex items-center px-2.5 py-0.5 my-0.5 rounded-full text-xs font-medium bg-sky-600 text-white">{{ $librarian }}</span>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        @if(!empty($campus->gcal))
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('campuses.edit', $campus->id) }}"
                           class="inline-flex items-center p-2 text-xs bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 dark:bg-emerald-800 dark:text-emerald-300 dark:hover:bg-emerald-700">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <style>
    .drag-handle:active {
        cursor: grabbing;
    }
    </style>
</div>
