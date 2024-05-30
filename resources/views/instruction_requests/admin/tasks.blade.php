<ul class="list-unstyled mb-4">
    <li class="mb-2"><strong>Tasks Completed</strong></li>
    <li>
        <select id="tasks-completed" name="tasks_completed[]" multiple="multiple" class="form-control">
            <option value="video" @if($instructionRequest->detail->video) selected @endif>Video</option>
            <option value="non_video" @if($instructionRequest->detail->non_video) selected @endif>Non video</option>
            <option value="modified_tutorial" @if($instructionRequest->detail->modified_tutorial) selected @endif>Modified Tutorial</option>
            <option value="embedded" @if($instructionRequest->detail->embedded) selected @endif>Embedded Librarian</option>
            <option value="research_guide" @if($instructionRequest->detail->research_guide) selected @endif>Research Guide</option>
            <option value="handout" @if($instructionRequest->detail->handout) selected @endif>Handout</option>
            <option value="developed_assigment" @if($instructionRequest->detail->developed_assignment) selected @endif>Developed Assignment</option>
            <option value="other_materials" @if($instructionRequest->detail->other_materials) selected @endif>Other</option>
        </select>
    </li>
    <li>
        <x-textarea
            name="other_describe"
            label="Describe Other"
            :value="$instructionRequest->detail->other_describe"
            classes="p-0 my-4"
        />
    </li>
</ul>
