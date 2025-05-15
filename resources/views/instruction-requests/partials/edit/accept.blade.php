<div class="mt-4 my-2">
    <div class="flex flex-col md:flex-row justify-center items-center gap-4 p-4 bg-amber-50 border border-amber-100 dark:bg-stone-700 dark:border-gray-600 text-black dark:text-gray-200 rounded-md"> {{-- Adjusted dark mode background and border colors for better contrast --}}
        <div>
            <strong>Click accept to service this request. Click reject to send it back to request scheduling.</strong>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('instructionRequests.accept', $instructionRequest->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-400 dark:bg-green-800 text-white font-semibold rounded-md hover:bg-green-500 dark:hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-700 focus:ring-offset-2 transition-colors">
                    Accept
                </button>
            </form>

            <form method="POST" action="{{ route('instructionRequests.reject', $instructionRequest->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white font-semibold rounded-md hover:bg-red-700 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-600 focus:ring-offset-2 transition-colors">
                    Reject
                </button>
            </form>
        </div>
    </div>
</div>
