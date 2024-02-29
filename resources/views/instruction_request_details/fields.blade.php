<!-- Instruction Request Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('instruction_request_id', 'Instruction Request Id:') !!}
    {!! Form::number('instruction_request_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Librarian Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('librarian_id', 'Librarian Id:') !!}
    {!! Form::select('librarian_id', null, ['class' => 'form-control custom-select']) !!}
</div>


<!-- Instruction Duration Field -->
<div class="form-group col-sm-6">
    {!! Form::label('instruction_duration', 'Instruction Duration:') !!}
    {!! Form::text('instruction_duration', null, ['class' => 'form-control']) !!}
</div>

<!-- Class Notes Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('class_notes', 'Class Notes:') !!}
    {!! Form::textarea('class_notes', null, ['class' => 'form-control']) !!}
</div>

<!-- Materials Field -->
<div class="form-group col-sm-6">
    {!! Form::label('materials', 'Materials:') !!}
    <div class="input-group">
        <div class="custom-file">
            {!! Form::file('materials', ['class' => 'custom-file-input']) !!}
            {!! Form::label('materials', 'Choose file', ['class' => 'custom-file-label']) !!}
        </div>
    </div>
</div>
<div class="clearfix"></div>


<!-- Assessment Notes Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('assessment_notes', 'Assessment Notes:') !!}
    {!! Form::textarea('assessment_notes', null, ['class' => 'form-control']) !!}
</div>

<!-- Assessments Field -->
<div class="form-group col-sm-6">
    {!! Form::label('assessments', 'Assessments:') !!}
    <div class="input-group">
        <div class="custom-file">
            {!! Form::file('assessments', ['class' => 'custom-file-input']) !!}
            {!! Form::label('assessments', 'Choose file', ['class' => 'custom-file-label']) !!}
        </div>
    </div>
</div>
<div class="clearfix"></div>
