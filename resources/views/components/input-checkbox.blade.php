{{-- components/input-checkbox.blade.php --}}
@props(
    ['name' => null,
    'label' => null,
    'checked' => false,
    'classes' => '',
    'helptext' => null,
    'target' => null,
    'value' => false
    ])

<div class=" {{ $classes }}">

    <div class="form-group">
        {{ Form::hidden($name, '0') }}
        <input type="checkbox"
               name="{{$name}}"
               value="1"
               @if($target)data-target="{{$target}}" class="toggle-checkbox" @endif
               @if($checked)checked @endif
        />
        <x-label label="{{ $label }}" :for="$name" classes="form-check-label" />
    </div>

    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif

</div>


