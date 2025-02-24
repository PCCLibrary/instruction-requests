<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Tasks Completed</label>
        <div class="flex flex-wrap gap-2">
            @foreach($availableTasks as $value => $label)
                <button
                    type="button"
                    wire:click="toggleTask('{{ $value }}')"
                    @class([
                        'px-3 py-1.5 rounded-full text-sm font-medium transition-colors',
                        'bg-sky-600 text-white hover:bg-sky-800' => in_array($value, $selectedTasks),
                        'bg-white text-gray-800 hover:bg-sky-200' => !in_array($value, $selectedTasks),
                    ])
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Hidden inputs for form submission --}}
        @foreach($availableTasks as $value => $label)
            <input
                type="hidden"
                name="{{ $value }}"
                value="{{ in_array($value, $selectedTasks) ? '1' : '0' }}"
            >
        @endforeach
    </div>

    @if(in_array('other_materials', $selectedTasks))
        <div class="mt-4">
            <label for="other_describe" class="block text-sm font-medium text-gray-700">
                Describe Other Materials
            </label>
            <textarea
                wire:model.live="otherDescription"
                name="other_describe"
                id="other_describe"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                rows="3"
            ></textarea>
        </div>
    @endif
</div>
