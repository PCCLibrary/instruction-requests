{{-- components/input-text.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => 'form-group', 'required' => false, 'disabled' => false ])

<div class="{{ $classes }}">
    @if($label)
    <x-label :label="$label" :name="$name" :required="$required" />
    @endif
    <input type="text"
           class="form-control"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, $value) }}"
           @if($helptext) aria-describedby="{{ $name }}-help" @endif
           @if($required)required @endif
           @if($disabled)disabled @endif
    />
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif
</div>
