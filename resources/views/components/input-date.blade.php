{{-- components/input-date.blade.php --}}
@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => null, 'required' => false])

<div class="space-y-1 {{ $classes }}">
    <x-label :value="$label" :for="$name" :required="$required" />
    <input type="date"
           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '' }}"
           @if($helptext) aria-describedby="{{ $name }}-help" @endif
        @required($required)
    />
    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif
</div>
