{{-- components/input-time.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => 'form-group', ])

<div class="{{ $classes }}">
    <x-label :label="$label" :required="$required" />
    <input type="time"
           class="form-control"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, $value) }}"
           @if($helptext)aria-describedby="{{ $name }}-help" @endif

    />
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif
</div>
