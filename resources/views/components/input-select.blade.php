{{-- components/input-select.blade.php --}}
@props(['name', 'label', 'options', 'selected' => null, 'showOther' => false, 'classes' => 'form-group', 'helptext' => null, 'tophelptext' => null, 'required' => false])

<div class="{{ $classes }}">
    <x-label :label="$label" :name="$name" :required="$required" />
    @if($tophelptext)
        <x-helptext name="{{ $name }}" helptext="{{ $tophelptext }}" />
    @endif
    <select class="form-control" name="{{ $name }}" id="{{ $name }}"
            @if($required)required @endif
    >
        <option value="">select {{ $label }}</option>
        @foreach($options as $id => $display_name)
            <option value="{{ $id }}" {{ (string) $id === (string) old($name, $selected) ? 'selected' : '' }}>{{ $display_name }}</option>
        @endforeach
        @if($showOther)
            <option value="other" {{ 'other' === old($name, $selected) ? 'selected' : '' }}>Other</option>
        @endif
    </select>
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif
</div>
