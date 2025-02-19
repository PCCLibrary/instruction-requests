{{-- components/url-manager.blade.php --}}
@props([
    'name',
    'label' => 'Enter URL or Text',
    'value' => '',
    'helptext' => null,
    'classes' => null,
    'required' => false,
    'disabled' => false
])

<div class="space-y-1 {{ $classes }}"
     x-data="{
        items: [],
        newItem: '',
        label: @js($label),

        init() {
            try {
                if (this.isJson(@js($value))) {
                    this.items = JSON.parse(@js($value));
                } else if (@js($value)) {
                    this.items = [@js($value)];
                }
            } catch (e) {
                console.error('Error initializing:', e);
                this.items = [];
            }
        },

        isJson(str) {
            try {
                JSON.parse(str);
                return true;
            } catch (e) {
                return false;
            }
        },

        addItem() {
            if (this.newItem.trim()) {
                this.items.push(this.newItem.trim());
                this.newItem = '';
            }
        },

        removeItem(index) {
            this.items = this.items.filter((_, i) => i !== index);
        }
     }">

    <x-input-label :value="$label" :for="$name" :required="$required" />

    {{-- Input Area --}}
    <div class="flex space-x-2 mb-4">
        <input
            type="text"
            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            x-model="newItem"
            @keydown.enter.prevent="addItem()"
            :placeholder="label"
        >
        <button
            type="button"
            @click="addItem()"
            :disabled="!newItem.trim()"
            class="px-3 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded disabled:opacity-50 disabled:cursor-not-allowed">
            Add
        </button>
    </div>

    {{-- List of Items --}}
    <ul class="mb-4">
        <template x-for="(item, index) in items" :key="index">
            <li class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md">
                <span x-text="item" class="break-all"></span>
                <button
                    type="button"
                    @click="removeItem(index)"
                    class="ml-2 text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </li>
        </template>
    </ul>

    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif

    {{-- Hidden Input for Form Submission --}}
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $name }}"
        :value="JSON.stringify(items)"
    />
</div>
