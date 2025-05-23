{{-- User filter toggle pills for PowerGrid table --}}
<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4">
    <div class="flex items-center space-x-4">
        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Show Users:</h3>

        <div class="flex items-center space-x-3">
            {{-- Active Users Toggle Pill --}}
            <button
                wire:click="$toggle('showActiveUsers')"
                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-colors duration-200 {{ $showActiveUsers ? 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-800' : 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600' }} border"
            >
                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    @if($showActiveUsers)
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    @else
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    @endif
                </svg>
                Active Users
            </button>

            {{-- Deleted Users Toggle Pill --}}
            <button
                wire:click="$toggle('showDeletedUsers')"
                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-colors duration-200 {{ $showDeletedUsers ? 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900 dark:text-red-200 dark:border-red-800' : 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600' }} border"
            >
                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    @if($showDeletedUsers)
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    @else
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    @endif
                </svg>
                Deleted Users
            </button>
        </div>

        @if(!$showActiveUsers && !$showDeletedUsers)
            <div class="inline-flex items-center px-3 py-1.5 bg-amber-100 border border-amber-200 rounded-full text-sm text-amber-800 dark:bg-amber-900 dark:border-amber-800 dark:text-amber-200">
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                No user types selected
            </div>
        @endif
    </div>
</div>
