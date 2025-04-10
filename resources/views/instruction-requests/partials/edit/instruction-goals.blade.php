{{-- Instruction Goals Section --}}
<x-card title="Instruction Goals and Discussion Topics" class="bg-gray-50 mb-4">
            <div class="space-y-4" x-data="{
                get isDisabled() {
                    return !$store.formState.isSectionEditable('instructionGoals');
                }
            }">
                <div>
                    <x-input-textarea
                        name="library_instruction_description"
                        label="What do you want your students to get out of library instruction?"
                        :value="$instructionRequest->library_instruction_description"
                        class="edit-field"
                        helptext="Examples: developing a topic, searching effectively, evaluating sources, etc."
                        x-bind:readonly="isDisabled"
                        x-bind:class="isDisabled ? 'bg-gray-100 cursor-not-allowed' : ''"
                    />
                </div>

                <div>
                    <x-input-textarea
                        name="genai_discussion_interest"
                        label="Generative AI Discussion Interest"
                        :value="$instructionRequest->genai_discussion_interest"
                        class="edit-field"
                        helptext="If you have class guidelines about ChatGPT, Perplexity, etc., or want to coordinate with your librarian on AI usage, share details here."
                        x-bind:readonly="isDisabled"
                        x-bind:class="isDisabled ? 'bg-gray-100 cursor-not-allowed' : ''"
                    />
                </div>
            </div>
</x-card>
