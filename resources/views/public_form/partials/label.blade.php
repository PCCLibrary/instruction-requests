{{-- components/label.blade.php --}}
@props(['label' => null, 'name' => null, 'class' => 'field-label', 'required' => null])

<label for="{{ $name }}" class=" {{ $class }} @if($required)is-required @endif">
    {!! $label !!}
</label>
