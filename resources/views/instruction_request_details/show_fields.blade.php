<!-- Instruction Request Id Field -->
<div class="col-sm-12">
    {!! Form::label('instruction_request_id', 'Instruction Request Id:') !!}
    <p>{{ $instructionRequestDetails->instruction_request_id }}</p>
</div>

<!-- Librarian Id Field -->
<div class="col-sm-12">
    {!! Form::label('librarian_id', 'Librarian Id:') !!}
    <p>{{ $instructionRequestDetails->librarian_id }}</p>
</div>

<!-- Tasks Completed Field -->
<div class="col-sm-12">
    {!! Form::label('tasks_completed', 'Tasks Completed:') !!}
    <p>{{ $instructionRequestDetails->tasks_completed }}</p>
</div>

<!-- Instruction Duration Field -->
<div class="col-sm-12">
    {!! Form::label('instruction_duration', 'Instruction Duration:') !!}
    <p>{{ $instructionRequestDetails->instruction_duration }}</p>
</div>

<!-- Class Notes Field -->
<div class="col-sm-12">
    {!! Form::label('class_notes', 'Class Notes:') !!}
    <p>{{ $instructionRequestDetails->class_notes }}</p>
</div>

<!-- Materials Field -->
<div class="col-sm-12">
    {!! Form::label('materials', 'Materials:') !!}
    <p>{{ $instructionRequestDetails->materials }}</p>
</div>

<!-- Assessment Notes Field -->
<div class="col-sm-12">
    {!! Form::label('assessment_notes', 'Assessment Notes:') !!}
    <p>{{ $instructionRequestDetails->assessment_notes }}</p>
</div>

<!-- Assessments Field -->
<div class="col-sm-12">
    {!! Form::label('assessments', 'Assessments:') !!}
    <p>{{ $instructionRequestDetails->assessments }}</p>
</div>

<!-- Created By Field -->
<div class="col-sm-12">
    {!! Form::label('created_by', 'Created By:') !!}
    <p>{{ $instructionRequestDetails->created_by }}</p>
</div>

<!-- Last Updated By Field -->
<div class="col-sm-12">
    {!! Form::label('last_updated_by', 'Last Updated By:') !!}
    <p>{{ $instructionRequestDetails->last_updated_by }}</p>
</div>

