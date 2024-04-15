@props([
    'instructionRequests',
    'title' => 'Instruction Requests',
    'headerClasses' => 'bg-lightblue',
    'showStatus' => false
])

<div class="card">
    <div class="card-header {{ $headerClasses }}">
        <h3 class="card-title">{{ $title }}</h3>

    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-striped table-head-fixed text-nowrap">
        <thead>
        <tr>
            <th></th>
            <th>Instructor Name</th>
            <th>Class Name</th>
            <th>Received</th>
            @if($showStatus)
            <th>Status</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @forelse($instructionRequests as $request)
            <tr>
                <td>
                    <a href="{{ route('instructionRequests.edit', $request->id) }}"
                       title="click to edit this request"
                    ><i class="fa fa-edit"></i></a>
                </td>
                <td>{{ $request->Instructor->display_name }}</td>
                <td>{{ $request->classes->course_name }}</td>
                <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d-g:i A') }}</td>
                @if($showStatus)
                <td>{{ $request->status }}</td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="5">No instruction requests found.</td>
            </tr>
        @endforelse
        </tbody>
        </table>
    </div>
</div>
