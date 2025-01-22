{{-- components/input-checkbox.blade.php --}}
@props(
    ['name' => null,
    'label' => null,
    'checked' => false,
    'classes' => null,
    'helptext' => null,
    'target' => null,
    'value' => false,
    'required' => false
    ])


    <div class="form-group {{ $classes }}">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox"
               name="{{$name}}"
               value="1"
               @if($target)data-target="{{$target}}" class="toggle-checkbox" @endif
               @if($checked)checked @endif
        />
        @include('public_form.partials.label', [
            'label' => $label,
            'name' => $name,
            'required' => $required
        ])
        @if($helptext)
            @include('public_form.partials.helptext', [
                'name' => $name,
                'helptext' => $helptext
            ])
        @endif
    </div>





