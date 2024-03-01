{{-- components/input-select.blade.php --}}
@props(['name', 'label', 'options', 'selected' => null, 'showOther' => false, 'classes' => 'form-group', 'helptext' => null, 'required' => false])

<div class="{{ $classes }}">
    <label for="{{ $name }}">{{ $label }}</label>
    <select class="form-control" name="{{ $name }}" id="{{ $name }}">
        @foreach($options as $id => $display_name)
            <option value="{{ $id }}" {{ (string) $id === (string) old($name, $selected) ? 'selected' : '' }}>{{ $display_name }}</option>
        @endforeach
        @if($showOther)
            <option value="other" {{ 'other' === old($name, $selected) ? 'selected' : '' }}>Other</option>
        @endif
    </select>
    @if($helptext)
        <small id="{{ $name }}-help" class="form-text text-muted">
            {{ $helptext }}
        </small>
    @endif
</div>
