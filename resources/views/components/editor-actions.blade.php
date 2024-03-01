<!-- resources/views/components/button-group.blade.php -->

@props(['route', 'showBack' => false, 'classes' => '', 'title'])

<div class="card {{ $classes }}">
    <div class="card-header bg-lightblue">
        <h3>{{ $title }}</h3>
    </div>
    <div class="card-body">
        <div class="button-group">
                {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
                <a href="{{ $route }}" class="btn btn-warning">Cancel</a>
        @if ($showBack)
                <a href="{{ $route }}" class="btn btn-info ml-4">Back to List</a>
        @endif
        </div>
    </div>
</div>
