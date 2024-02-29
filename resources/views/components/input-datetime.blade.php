{{-- components/input-datetime.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null ])

<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <input type="datetime-local"
           class="form-control"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, str_replace(' ', 'T', $value)) }}"
           @if($helptext) aria-describedby="{{ $name }}-help" @endif
    />
    @if($helptext)
        <small id="{{ $name }}-help" class="form-text text-muted">
            {{ $helptext }}
        </small>
    @endif
</div>
