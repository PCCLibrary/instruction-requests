{{-- components/input-select.blade.php --}}
@props(['name', 'label', 'options', 'selected' => null, 'showOther' => false, 'classes' => null, 'helptext' => null, 'tophelptext' => null, 'required' => false, 'disabled' => false])

<div class="space-y-1 {{ $classes }}">
    <x-label :value="$label" :for="$name" :required="$required" />
    @if($tophelptext)
        <x-helptext :name="$name" :helptext="$tophelptext" />
    @endif
    <select
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        name="{{ $name }}"
        id="{{ $name }}"
        @required($required)
        @disabled($disabled)
    >
        <option value="">Select {{ $label }}</option>
        @foreach($options as $id => $display_name)
            <option value="{{ $id }}" @selected(old($name, $selected) == $id)>
                {{ $display_name }}
            </option>
        @endforeach
        @if($showOther)
            <option value="other" @selected(old($name, $selected) == 'other')>Other</option>
        @endif
    </select>
    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif
</div>
