<x-editor-actions
    route="{{ route('instructionRequests.index') }}"
    :showBack="(bool)$instructionRequest"
/>

@if($instructionRequest)

    <ul class="list-unstyled mt-4">

        <li><strong>created by: </strong>{{ $instructionRequest->detail->created_by }}</li>
        <li><strong>last updated by: </strong>{{ $instructionRequest->detail->last_updated_by }}</li>
        <li><strong>on: </strong>{{ \Carbon\Carbon::parse($instructionRequest->detail->updated_at)->format('M d g:i A') }}</li>

    </ul>

@endif
