@props(['name', 'label', 'value' => '', 'helptext' => null, 'classes' => null, 'required' => false, 'disabled' => false])

<div class="space-y-1 {{ $classes }}">
    @if($label)
        <x-input-label :value="$label" :for="$name" :required="$required" />
    @endif
    <input type="tel"
           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, $value) }}"
           @if($helptext) aria-describedby="{{ $name }}-help" @endif
        @required($required)
        @disabled($disabled)
    />
    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif
</div>
