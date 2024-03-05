{{-- components/input-file.blade.php --}}
@props(['name', 'label', 'accept' => '', 'classes' => 'form-group', 'helptext' => null, 'required' => false])

<div class="{{ $classes }}">
    <label>{{ $label }}</label>
    <input type="file" name="{{ $name }}" accept="{{ $accept }}" {{ $attributes }}>
    @if($helptext)
        <small id="{{ $name }}-help" class="form-text text-muted">
            {{ $helptext }}
        </small>
    @endif
</div>
