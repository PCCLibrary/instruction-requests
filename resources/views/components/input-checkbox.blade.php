{{-- components/input-checkbox.blade.php --}}
@props([
    'name',
    'label',
    'checked' => false,
    'classes' => null,
    'helptext' => null,
    'required' => false,
    'disabled' => false
])

<div class="relative flex items-start {{ $classes }}" x-data>
    <div class="flex items-center h-5">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox"
               name="{{ $name }}"
               id="{{ $name }}"
               value="1"
               @checked(old($name, $checked))
               x-bind:disabled="$store.editFormState?.isEditing === false"
            @class([
                'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:ring-offset-gray-800',
                 'bg-white border-gray-300 shadow-sm' => !$disabled,
                 'bg-gray-100 border-gray-200' => $disabled
            ])
        />
    </div>
    <div class="ml-3 text-sm">
        <x-input-label :value="$label" :for="$name" :required="$required" />
        @if($helptext)
            <x-helptext :name="$name" :helptext="$helptext" />
        @endif
    </div>
</div>
