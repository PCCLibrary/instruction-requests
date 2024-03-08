{{-- components/label.blade.php --}}
@props(['label' => null, 'name' => null, 'classes' => 'field-label', 'required' => null])

<label class="{{$classes}}">
    {!! $label !!} @if($required)<span class="text-danger"> *</span>@endif
</label>
