<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\InstructionRequests;

class UpdateInstructionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            // Original request fields that can be updated
            'campus_id' => 'sometimes|required|exists:campuses,id',
            'instruction_type' => 'sometimes|required|string',
            'department' => 'sometimes|nullable|string',
            'course_number' => 'sometimes|nullable|string',
            'course_crn' => 'sometimes|nullable|string',
            'number_of_students' => 'sometimes|nullable|integer',
            'preferred_datetime' => 'sometimes|nullable|date',
            'alternate_datetime' => 'sometimes|nullable|date',
            'duration' => 'sometimes|nullable|string',
            'asynchronous_instruction_ready_date' => 'sometimes|nullable|date',
            'status' => 'sometimes|required|string|in:received,assigned,accepted,rejected,completed',

            // Details fields that can be updated
            'assigned_librarian_id' => 'sometimes|nullable|exists:users,id',  // Added this
            'instruction_duration' => 'sometimes|nullable|string',
            'instruction_datetime' => 'sometimes|nullable|date',
            'class_notes' => 'sometimes|nullable|string',
            'video' => 'sometimes|boolean',
            'non_video' => 'sometimes|boolean',
            'modified_tutorial' => 'sometimes|boolean',
            'embedded' => 'sometimes|boolean',
            'research_guide' => 'sometimes|boolean',
            'handout' => 'sometimes|boolean',
            'developed_assignment' => 'sometimes|boolean',
            'other_materials' => 'sometimes|boolean',
            'other_describe' => 'sometimes|nullable|string',
            'assessment_notes' => 'sometimes|nullable|string',
            'materials.*' => 'sometimes|file|mimes:txt,rtf,pdf,doc,docx|max:20480',
            'assessments.*' => 'sometimes|file|mimes:txt,rtf,pdf,doc,docx|max:20480',
            'room' => 'sometimes|nullable|string',
        ];

        // Apply conditional rules based on instruction type
        if ($this->filled('instruction_type')) {
            switch ($this->input('instruction_type')) {
                case 'on-campus':
                case 'remote':
                    $rules['preferred_datetime'] = 'sometimes|required|date';
                    $rules['duration'] = 'sometimes|required|string';
                    break;
                case 'asynchronous':
                    $rules['asynchronous_instruction_ready_date'] = 'sometimes|required|date';
                    break;
            }
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        // Cast boolean fields
        $booleanFields = [
            'video',
            'non_video',
            'modified_tutorial',
            'embedded',
            'research_guide',
            'handout',
            'developed_assignment',
            'other_materials'
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN)
                ]);
            }
        }
    }
}
