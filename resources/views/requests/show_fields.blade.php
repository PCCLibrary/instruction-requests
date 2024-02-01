<!-- Id Field -->
<div class="col-sm-12">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $request->id }}</p>
</div>

<!-- Classname Field -->
<div class="col-sm-12">
    {!! Form::label('classname', 'Classname:') !!}
    <p>{{ $request->classname }}</p>
</div>

<!-- Description Field -->
<div class="col-sm-12">
    {!! Form::label('description', 'Description:') !!}
    <p>{{ $request->description }}</p>
</div>

