{{-- components/input-datetime.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => 'form-group', 'required' => false, 'disabled' => false ])

<div class="{{ $classes }}">
    @if($label)
        @include('public_form.partials.label', [
            'label' => $label,
            'name' => $name,
            'required' => $required
        ])
    @endif

    @php
        // Process the datetime to remove seconds
        $formattedValue = '';
        if ($value) {
            $date = \Carbon\Carbon::parse($value);
            $date->setSecond(0);
            $formattedValue = $date->format('Y-m-d\TH:i');
        }
    @endphp

    <input type="datetime-local"
           class="form-control"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, $formattedValue) }}"
           step="60"
           @if($helptext) aria-describedby="{{ $name }}-help" @endif
           @if($required) required @endif
           @if($disabled) disabled @endif
    />

    @if($helptext)
        @include('public_form.partials.helptext', [
            'name' => $name,
            'helptext' => $helptext
        ])
    @endif
</div>
