<div class="mt-4 my-2">
    <div class="flex flex-col md:flex-row justify-center items-center gap-4 p-4 bg-amber-50 border border-amber-100 rounded-md">
        <div>
            <strong>Click accept to service this request. Click reject to send it back to request scheduling.</strong>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('instructionRequests.accept', $instructionRequest->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    Accept
                </button>
            </form>

            <form method="POST" action="{{ route('instructionRequests.reject', $instructionRequest->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    Reject
                </button>
            </form>
        </div>
    </div>
</div>
