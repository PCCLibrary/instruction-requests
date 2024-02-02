<!-- Instructor Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('instructor_name', 'Instructor Name:') !!}
    {!! Form::text('instructor_name', null, ['class' => 'form-control']) !!}
</div>

<!-- Display Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('display_name', 'Display Name:') !!}
    {!! Form::text('display_name', null, ['class' => 'form-control']) !!}
</div>

<!-- Instructor Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('instructor_email', 'Instructor Email:') !!}
    {!! Form::text('instructor_email', null, ['class' => 'form-control']) !!}
</div>

<!-- Instructor Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('instructor_phone', 'Instructor Phone:') !!}
    {!! Form::text('instructor_phone', null, ['class' => 'form-control']) !!}
</div>

<!-- Instruction Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('instruction_type', 'Instruction Type:') !!}
    {!! Form::select('instruction_type', ['Synchronous' => 'Synchronous', 'Asynchronous' => 'Asynchronous'], null, ['class' => 'form-control custom-select']) !!}
</div>


<!-- Course Modality Field -->
<div class="form-group col-sm-6">
    {!! Form::label('course_modality', 'Course Modality:') !!}
    {!! Form::select('course_modality', ['Online' => 'Online', 'In-person' => 'In-person', 'Hybrid' => 'Hybrid'], null, ['class' => 'form-control custom-select']) !!}
</div>


<!-- Librarian Field -->
{{--<div class="form-group col-sm-6">--}}
{{--    {!! Form::label('librarian', 'Librarian:') !!}--}}
{{--    {!! Form::select('librarian', ['' => ''], null, ['class' => 'form-control custom-select']) !!}--}}
{{--</div>--}}
<!-- Librarian Field -->
<div class="form-group col-sm-6">
    {!! Form::label('librarian', 'Librarian:') !!}
    {!! Form::select('librarian', $librarians->pluck('name','id')->prepend('Select Librarian', ''), null, ['class' => 'form-control custom-select']) !!}
</div>


<!-- Class Location Field -->
{{--<div class="form-group col-sm-6">--}}
{{--    {!! Form::label('class_location', 'Class Location:') !!}--}}
{{--    {!! Form::select('class_location', ['' => ''], null, ['class' => 'form-control custom-select']) !!}--}}
{{--</div>--}}
<!-- Class Location Field -->
<div class="form-group col-sm-6">
    {!! Form::label('class_location', 'Class Location:') !!}
    {!! Form::select('class_location', $classLocations->pluck('name','id')->prepend('Select Class Location', ''), null, ['class' => 'form-control custom-select']) !!}
</div>


<!-- Course Department Field -->
<div class="form-group col-sm-6">
    {!! Form::label('course_department', 'Course Department:') !!}
    {!! Form::text('course_department', null, ['class' => 'form-control']) !!}
</div>

<!-- Course Number Field -->
<div class="form-group col-sm-6">
    {!! Form::label('course_number', 'Course Number:') !!}
    {!! Form::text('course_number', null, ['class' => 'form-control']) !!}
</div>
