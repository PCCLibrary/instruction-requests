{{-- components/input-checkbox.blade.php --}}
@props([
    'name' => null,
    'label' => null,
    'checked' => false,
    'classes' => null,
    'helptext' => null,
    'target' => null,
    'value' => false
])

<div class="relative flex items-start {{ $classes }}">
    <div class="flex items-center h-5">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox"
               name="{{ $name }}"
               id="{{ $name }}"
               value="1"
               @checked(old($name, $checked))
               @class([
                   'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:ring-offset-gray-800',
                   'toggle-checkbox' => $target
               ])
               @if($target) data-target="{{ $target }}" @endif
        />
    </div>
    <div class="ml-3 text-sm">
        <x-label :for="$name" :value="$label" class="font-medium text-gray-700 dark:text-gray-300" />
        @if($helptext)
            <x-helptext :name="$name" :helptext="$helptext" />
        @endif
    </div>
</div>
