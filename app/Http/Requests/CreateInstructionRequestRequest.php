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
    public function rules(): array
    {
        return InstructionRequest::rules($this);
    }

    /**
     * Customize the error messages for files
     *
     * @return string[]
     */
    public function messages()
    {
        return [
            'class_syllabus.*.mimes' => 'Each class syllabus file must be a type of: :values.',
            'class_syllabus.*.max' => 'Each class syllabus file may not be greater than :max kilobytes.',
            'instructor_attachments.*.mimes' => 'Each instructor attachment file must be a type of: :values.',
            'instructor_attachments.*.max' => 'Each instructor attachment file may not be greater than :max kilobytes.',
        ];
    }


}
