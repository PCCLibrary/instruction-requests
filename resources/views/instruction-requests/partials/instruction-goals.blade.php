{{-- Instruction Goals Section --}}
<x-card title="Instruction Goals and Discussion Topics" class="bg-gray-50 mb-4">
            <div>
                <x-input-textarea
                    name="library_instruction_description"
                    label="What do you want your students to get out of library instruction?"
                    :value="$instructionRequest->library_instruction_description"
                    disabled="!isEditing"
                    class="edit-field"
                    helptext="Examples: developing a topic, searching effectively, evaluating sources, etc."
                />
            </div>

            <div>
                <x-input-textarea
                    name="genai_discussion_interest"
                    label="Generative AI Discussion Interest"
                    :value="$instructionRequest->genai_discussion_interest"
                    disabled="!isEditing"
                    class="edit-field"
                    helptext="If you have class guidelines about ChatGPT, Perplexity, etc., or want to coordinate with your librarian on AI usage, share details here."
                />
            </div>
</x-card>
