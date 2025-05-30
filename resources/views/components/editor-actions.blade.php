@props(['route', 'showSubmit'=>true,'showBack' => false, 'unlockRequest' => false])


<div class="flex flex-wrap justify-between items-center gap-4">

    <div class="space-x-2">
        @if ($showSubmit)
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-green-800 dark:hover:bg-green-900 dark:focus:bg-green-800 dark:active:bg-green-950 dark:text-white dark:border-transparent dark:focus:ring-green-600 dark:focus:ring-offset-gray-800"
            >
                Save
            </button>
        @endif

        <button
            type="submit"
            name="saveAndClose"
            value="1"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-blue-800 dark:hover:bg-blue-900 dark:focus:bg-blue-800 dark:active:bg-blue-950 dark:text-white dark:border-transparent dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800"
        >
            Save & Close
        </button>

        @if ($unlockRequest)
            <a
                href="{{ route('instructionRequests.cancelUnlock', request()->route('instructionRequest')) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-yellow-700 dark:hover:bg-yellow-800 dark:focus:bg-yellow-800 dark:active:bg-yellow-900 dark:text-white dark:border-transparent dark:focus:ring-yellow-600 dark:focus:ring-offset-gray-800"
            >
                Cancel
            </a>
        @else
            <a
                href="{{ $route }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-yellow-700 dark:hover:bg-yellow-800 dark:focus:bg-yellow-800 dark:active:bg-yellow-900 dark:text-white dark:border-transparent dark:focus:ring-yellow-600 dark:focus:ring-offset-gray-800"
            >
                Cancel
            </a>
        @endif
    </div>

    @if ($showBack)
        <div>
            <a
                href="{{ $route }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:active:bg-gray-800 dark:text-white dark:border-transparent dark:focus:ring-gray-400 dark:focus:ring-offset-gray-800"
            >
                Back to List
            </a>
        </div>
    @endif
</div>
