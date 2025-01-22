{{-- components/input-radio.blade.php --}}
@props(['name', 'label', 'options', 'selected' => null, 'showOther' => false, 'classes' => null, 'helptext' => null, 'required' => false])

<div class="space-y-1 {{ $classes }}" id="{{ $name }}">
    <x-label :value="$label" :for="$name" :required="$required" />
    <div class="space-y-2">
        @foreach($options as $value => $text)
            <div class="flex items-center">
                <input
                    type="radio"
                    name="{{ $name }}"
                    id="{{ $name }}_{{ $value }}"
                    value="{{ $value }}"
                    @checked(old($name, $selected) == $value)
                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900"
                />
                <label class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300" for="{{ $name }}_{{ $value }}">
                    {{ $text }}
                </label>
            </div>
        @endforeach
        @if($showOther)
            <div class="flex items-center">
                <input
                    type="radio"
                    name="{{ $name }}"
                    id="{{ $name }}_other"
                    value="other"
                    @checked(old($name, $selected) == 'other')
                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900"
                />
                <label class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300" for="{{ $name }}_other">
                    Other
                </label>
            </div>
        @endif
    </div>
    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif
</div>
