{{-- components/input-radio-group.blade.php --}}
@props(['name', 'label', 'options', 'selected' => null, 'showOther' => false, 'classes' => 'form-group', 'helptext' => null, 'required' => false])

<div class="{{ $classes }}" id="{{ $name }}">
    <x-label :label="$label" :required="$required" />
    <div class="form-group d-flex">
        @foreach($options as $value => $text)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="{{ $name }}" id="{{ $name }}_{{ $value }}" value="{{ $value }}" {{ (string) $value === (string) old($name, $selected) ? 'checked' : '' }}>
                <label class="form-check-label mr-4 mb-2" for="{{ $name }}_{{ $value }}">
                    {{ $text }}
                </label>
            </div>
        @endforeach
        @if($showOther)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="{{ $name }}" id="{{ $name }}_other" value="other" {{ (string) 'other' === (string) old($name, $selected) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $name }}_other">
                    Other
                </label>
            </div>
        @endif
    </div>
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif
</div>
