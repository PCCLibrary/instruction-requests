<div class="mt-4">
    <div class="row justify-content-center align-items-center p-4" style="background-color: #fff9e0; border: 1px solid #0088A4; border-radius: 4px;">
        <div class="col-auto">
            <strong>Click accept to service this request. Click reject to send it back to request scheduling.</strong>
        </div>
        <div class="col-auto">
            <form method="POST" action="{{ route('instructionRequests.accept', $instructionRequest->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-success">Accept</button>
            </form>

            <form method="POST" action="{{ route('instructionRequests.reject', $instructionRequest->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Reject</button>
            </form>
        </div>
    </div>
</div>
