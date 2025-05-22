{{-- /views/instruction-requests/partials/create/instruction-goals.blade.php --}}
<x-fieldset legend="Instruction Goals and Discussion Topics" classes="bg-white dark:bg-gray-800 on-campus-fields remote-fields asynchronous-fields">
    <div class="space-y-6">
        <div>
            <x-input-textarea
                name="library_instruction_description"
                label="What do you want your students to get out of library instruction?"
                :value="old('library_instruction_description')"
                help-text="Examples: developing a topic, searching effectively, evaluating sources, etc."
            />
        </div>

        <div>
            <x-input-textarea
                name="genai_discussion_interest"
                label="Generative AI Discussion Interest"
                :value="old('genai_discussion_interest')"
                help-text="If you have class guidelines about ChatGPT, Perplexity, etc., or want to coordinate with your librarian on AI usage, share details here."
            />
        </div>
    </div>
</x-fieldset>
