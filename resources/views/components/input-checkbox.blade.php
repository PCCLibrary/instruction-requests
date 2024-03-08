{{-- components/input-checkbox.blade.php --}}
@props(['name', 'label', 'checked' => false, 'classes' => '', 'helptext' => null, 'value' => false ])

<div class=" {{ $classes }}">

    <div class="form-group">
        {{ Form::hidden($name, '0') }}
        {{ Form::checkbox($name,  '1',  $checked) }}
        <x-label label="{{ $label }}" classes="form-check-label" />
    </div>

    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif

</div>


