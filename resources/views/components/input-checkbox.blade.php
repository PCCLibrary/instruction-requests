{{-- components/input-checkbox.blade.php --}}
@props([
    'name' => null,
    'label' => null,
    'checked' => false,
    'classes' => null,
    'helptext' => null,
    'target' => null,
    'value' => false
])

<div class="form-group {{ $classes }}">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox"
           name="{{ $name }}"
           id="{{ $name }}"
           value="1"
           @if($target) data-target="{{ $target }}" class="toggle-checkbox" @endif
           @if($checked) checked @endif
    />
    <x-label :label="$label" :for="$name" classes="form-check-label" />
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif
</div>
