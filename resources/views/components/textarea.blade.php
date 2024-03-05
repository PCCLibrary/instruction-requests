{{-- components/textarea.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => 'col-auto', ])

<div class="form-group {{ $classes }}">
    <label for="{{ $name }}">{{ $label }}</label>
    <textarea class="form-control"
              name="{{ $name }}"
              id="{{ $name }}"
              rows="3"
              @if($helptext) aria-describedby="{{ $name }}-help" @endif
    >{{ old($name, $value) }}</textarea>

    @if($helptext)
        <small id="{{ $name }}-help" class="form-text text-muted">
            {{ $helptext }}
        </small>
    @endif
</div>