{{-- components/input-text.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => 'form-group', 'required' => false ])

<div class="{{ $classes }}">
    @include('public_form.partials.label', [
                'label' => $label,
                'name' => $name,
                'required' => $required
            ])
    <input type="text"
           class="form-control html-duration-picker"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, $value) }}"
           @if($helptext) aria-describedby="{{ $name }}-help" @endif
           @if($required)required @endif
           data-hide-seconds
    />
    @if($helptext)
        @include('public_form.partials.helptext', [
            'name' => $name,
            'helptext' => $helptext
        ])
    @endif
</div>
