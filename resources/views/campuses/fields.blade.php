<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Code Field -->
<div class="form-group col-sm-6">
    {!! Form::label('code', 'Code:') !!}
    {!! Form::text('code', null, ['class' => 'form-control']) !!}
</div>

<!-- Librarians Field -->
<div class="form-group col-sm-12">
    {!! Form::label('librarian_ids[]', 'Send notifications to:') !!}
    {!! Form::hidden('librarian_ids[]', '') !!}
    {!! Form::select('librarian_ids[]', $librarians, $campus->librarian_ids, ['id' => 'librarian_ids', 'class' => 'form-control', 'multiple' => 'multiple']) !!}
    <p class="mt-2 text-gray-500">Librarians to receive notifications from requests at this location.</p>
</div>
