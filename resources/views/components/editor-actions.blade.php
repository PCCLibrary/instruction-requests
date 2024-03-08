<!-- resources/views/components/button-group.blade.php -->

@props(['route', 'showBack' => false ])

<div class="d-flex justify-content-between">
<div class="button-group ">
        {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
        <a href="{{ $route }}" class="btn btn-warning">Cancel</a>
</div>
@if ($showBack)
    <div class="button-group":>
        <a href="{{ $route }}" class="btn btn-dark ml-4">Back to List</a>
    </div>
@endif
</div>
