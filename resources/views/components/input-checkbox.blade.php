{{-- components/input-checkbox.blade.php --}}
@props(['name', 'label', 'checked' => false, 'classes' => '', 'helptext' => null ])

<div class=" {{ $classes }}">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="{{ $name }}" id="{{ $name }}" value="1" {{ $checked ? 'checked' : '' }}>
        <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
    </div>
    @if($helptext)
        <small id="{{ $name }}-help" class="form-text text-muted">
            {{ $helptext }}
        </small>
    @endif
</div>
