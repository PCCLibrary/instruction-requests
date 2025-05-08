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
        }
    };
</script>
