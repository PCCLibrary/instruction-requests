<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\InstructionRequest;

class CreateInstructionRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'librarian_id' => 'required|exists:users,id',
            'campus_id' => 'required|exists:campuses,id',
//            'instructor_id' => 'required|exists:instructors,id', //
            // Common rules for both create and edit
            'instruction_type' => 'required|string',
            'course_modality' => 'required|string',
            'department' => 'nullable|string',
            'course_number' => 'nullable|string',
            'course_crn' => 'nullable|string',
            'number_of_students' => 'nullable|integer',
            'ada_provisions_needed' => 'nullable|boolean',
            'ada_provisions_description' => 'nullable|string',
            'preferred_datetime' => 'nullable|date',
            'alternate_datetime' => 'nullable|date',
            'duration' => 'nullable|string',
            'asynchronous_instruction_ready_date' => 'nullable|date',
            'need_extra_time' => 'nullable|boolean',
            'extra_time_with_class' => 'nullable|string',
            'received_assignment' => 'nullable|boolean',
            'selected_topics' => 'nullable|boolean',
            'explored_background' => 'nullable|boolean',
            'written_draft' => 'nullable|boolean',
            'other_learning_outcome' => 'nullable|boolean',
            'other_learning_outcome_description' => 'nullable|string',
        ];

        // Additional rules for creation
        if ($this->isMethod('post')) {
            $rules += [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'display_name' => 'nullable|string|max:255',
                'pronouns' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
            ];
        }

        // Additional rules for edit to ensure relationship integrity
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules += [
                'librarian_id' => 'required|exists:users,id',
                'campus_id' => 'required|exists:campuses,id',
                'instructor_id' => 'required|exists:instructors,id', // Ensure this is corrected to match your database structure
            ];
        }

        return $rules;
    }


}
