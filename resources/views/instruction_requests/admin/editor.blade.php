<x-editor-actions
    route="{{ route('instructionRequests.index') }}"
    :showBack="(bool)$instructionRequest"
/>

@if($instructionRequest)

<x-input-select name="status"
                label="Request Status"
                :options="['pending' => 'Pending', 'copied' => 'Copied', 'in progress' => 'In Progress', 'completed' => 'Completed']"
                :selected="old('status', $instructionRequest->status ?? null)"
                classes="col-12 p-0 m-0 mt-4"
/>

<ul class="list-unstyled mt-4">

    <li><strong>created by: </strong>{{ $instructionRequest->detail->created_by }}</li>
    <li><strong>last updated by: </strong>{{ $instructionRequest->detail->last_updated_by }}</li>
    <li><strong>on: </strong>{{ \Carbon\Carbon::parse($instructionRequest->detail->updated_at)->format('M d g:i A') }}</li>

</ul>
@endif
