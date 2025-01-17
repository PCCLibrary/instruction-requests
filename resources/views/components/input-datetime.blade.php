{{-- components/input-datetime.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => 'form-group', 'required' => false ])

<div class="{{ $classes }}">
    <x-label :label="$label" :name="$name" :required="$required" />
    <input type="datetime-local"
           class="form-control"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, str_replace(' ', 'T', $value)) }}"
           @if($helptext) aria-describedby="{{ $name }}-help" @endif
         @if($required)required @endif
    />
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif

</div>
