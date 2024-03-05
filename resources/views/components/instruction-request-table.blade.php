@props(['instructionRequests'])

<div class="card">
    <div class="card-header bg-lightblue">
        <h3 class="card-title">Pending Instruction Requests</h3>

    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-head-fixed text-nowrap">
        <thead>
        <tr>
            <th>Instructor Name</th>
            <th>Class Name</th>
            <th>Type</th>
            <th>Preferred Date & time</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($instructionRequests as $request)
            <tr>
                <td><a href="{{ route('instructors.edit', $request->Instructor->id) }}"><i class="fa fa-edit"></i> {{ $request->Instructor->display_name }}</a></td>
                <td>{{ $request->classes->course_name }}</td>
                <td>{{ $request->instruction_type }}</td>
                <td>{{ \Carbon\Carbon::parse($request->preferred_datetime)->format('M d-g:i A') }}</td>
                <td>
                    @include('instruction_requests.datatables_actions', ['id' => $request->id])
                </td>
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
