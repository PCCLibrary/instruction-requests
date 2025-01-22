<div class="mb-4">
<a href="{{ route('instructionRequests.edit', $instructionRequest->id) }}" class="btn btn-primary mb-2">Manage this request</a>
<a href="{{ route('instructionRequests.index') }}" class="btn btn-secondary mb-2">View All Requests</a>
</div>

@if($instructionRequest)

    <p><strong>Status:</strong> {{ $instructionRequest->status }}</p>

    <ul class="list-unstyled mt-4">

        <li><strong>created by: </strong>{{ $instructionRequest->detail->created_by }}</li>
        <li><strong>last updated by: </strong>{{ $instructionRequest->detail->last_updated_by }}</li>
        <li><strong>on: </strong>{{ \Carbon\Carbon::parse($instructionRequest->detail->updated_at)->format('M d g:i A') }}</li>

    </ul>
@endif
