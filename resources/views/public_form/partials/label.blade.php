{{-- components/label.blade.php --}}
@props(['label' => null, 'name' => null, 'classes' => 'field-label', 'required' => null])

<label for="{{ $name }}" class="{field-label @if($required)is-required @endif">
    {!! $label !!}
</label>
