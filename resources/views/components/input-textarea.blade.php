{{-- components/input-textarea.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => 'col-auto', 'required' => null ])

<div id="{{ $name }}" class="form-group {{ $classes }}">
    <x-label :label="$label" :required="$required" />
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif
    <textarea class="form-control"
              name="{{ $name }}"
              id="{{ $name }}"
              rows="3"
              @if($helptext) aria-describedby="{{ $name }}-help" @endif
              @if($required)required @endif
    >{{ old($name, $value) }}</textarea>
</div>
