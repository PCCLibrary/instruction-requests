@props(['instructionRequests'])

<div class="card">
    <div class="card-header bg-lightblue">
        <h3 class="card-title">Pending Instruction Requests</h3>

    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-striped table-head-fixed text-nowrap">
        <thead>
        <tr>
            <th></th>
            <th>Instructor Name</th>
            <th>Class Name</th>
            <th>Preferred Date & time</th>
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
                <td>{{ \Carbon\Carbon::parse($request->preferred_datetime)->format('M d-g:i A') }}</td>
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
