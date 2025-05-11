{{-- Create global JavaScript variables for lock-refresh.js to use --}}
<script>
    // Configuration object for lock-refresh.js
    window.lockConfig = {
        // URL for the index page
        indexUrl: "{{ route('instructionRequests.index') }}",

        // Function to generate refresh and release lock URLs
        getRefreshLockUrl: function(requestId) {
            return "{{ url('/dashboard/instructionRequests') }}/" + requestId + "/refresh-lock";
        },
        getReleaseLockUrl: function(requestId) {
            return "{{ url('/dashboard/instructionRequests') }}/" + requestId + "/release-lock";
        },

        // Always include current user ID for permission checks
        currentUserId: {{ auth()->id() }}

        @if(isset($instructionRequest))
        ,
        // Current lock state
        lockState: {
            requestId: {{ $instructionRequest->id }},
            isLocked: {{ $instructionRequest->isLocked() ? 'true' : 'false' }},
            lockedBy: {{ $instructionRequest->locked_by ?: 'null' }},
            lockedAt: "{{ $instructionRequest->locked_at ?? '' }}",
            hasLock: {{ ($instructionRequest->locked_by == auth()->id()) ? 'true' : 'false' }}
        }
        @endif
    };
</script>
