{{-- components/input-date.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => 'form-group',])

<div class="{{ $classes }}">
    <label for="{{ $name }}">{{ $label }}</label>
    <input type="date"
           class="form-control"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, $value) }}"
           @if($helptext) aria-describedby="{{ $name }}-help" @endif

    />
    @if($helptext)
        <small id="{{ $name }}-help" class="form-text text-muted">
            {{ $helptext }}
        </small>
    @endif
</div>
