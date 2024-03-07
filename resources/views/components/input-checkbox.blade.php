{{-- components/input-checkbox.blade.php --}}
@props(['name', 'label', 'checked' => false, 'classes' => '', 'helptext' => null, 'value' => false ])

<div class=" {{ $classes }}">

    <div class="form-group">
        {{ Form::checkbox($name,  '1',  $checked) }}
        {{ Form::label($name, null, ['class' => 'form-check-label', 'for' => $name]) }}
    </div>

{{--    <pre>{{  print_r(json_encode($checked), true) }}</pre>--}}

    @if($helptext)
        <small id="{{ $name }}-help" class="form-text text-muted">
            {{ $helptext }}
        </small>
    @endif
</div>


