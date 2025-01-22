{{-- components/input-checkbox-group.blade.php --}}
@props(['checkboxes', 'label', 'classes' => 'form-group', 'helptext' => null, 'required' => false])

<div class="{{ $classes }}">
    <label>{{ $label }}</label>
    @foreach($checkboxes as $checkbox)
        <div class="form-check">

            <input type="checkbox"
                   name="{{ $checkbox['name'] }}"
                   id="{{ $checkbox['name'] }}"
                   class="form-check-input"
                   value="{{ $checkbox['value'] ? '1' : '0' }}"
                    {{ $checkbox['value'] ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $checkbox['name'] }}">
                {{ $checkbox['label'] }}
            </label>
        </div>
    @endforeach
    @if($helptext)
        <small id="{{ $checkboxes[0]['name'] }}-help" class="form-text text-muted">
            {{ $helptext }}
        </small>
    @endif
</div>
