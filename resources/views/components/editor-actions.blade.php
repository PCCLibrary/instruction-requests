<!-- resources/views/components/button-group.blade.php -->

@props(['route', 'showBack' => false ])

<div class="button-group">
        {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
        <a href="{{ $route }}" class="btn btn-warning">Cancel</a>
@if ($showBack)
        <a href="{{ $route }}" class="btn btn-dark ml-4">Back to List</a>
@endif
</div>
