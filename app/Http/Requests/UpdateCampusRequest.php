<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampusRequest extends FormRequest
{
    private const NAME_MAX_LENGTH = 255;
    private const CODE_MAX_LENGTH = 50;
    private const GCAL_MAX_LENGTH = 255;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // No granular permissions required
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // Retrieve the campus ID from the route
        $campusId = $this->route('campus');

        return [
            // Campus name must be unique except for the current campus record
            'name' => [
                'required',
                'string',
                'max:' . self::NAME_MAX_LENGTH,
                Rule::unique('campuses', 'name')->ignore($campusId)
            ],

            // Campus code must be unique except for the current campus record
            'code' => [
                'required',
                'string',
                'max:' . self::CODE_MAX_LENGTH,
                Rule::unique('campuses', 'code')->ignore($campusId)
            ],

            // Google Calendar link must be a valid URL if provided
            'gcal' => [
                'nullable',
                'url',
                'max:' . self::GCAL_MAX_LENGTH
            ],

            // Librarian IDs should be an array, and each value must exist in the `users` table
            'librarian_ids' => [
                'nullable',
                'array'
            ],
            'librarian_ids.*' => [
                'exists:users,id'
            ]
        ];
    }

    /**
     * Custom error messages for validation failures.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // Unique field error messages
            'name.unique' => 'A campus with this name already exists.',
            'code.unique' => 'A campus with this code already exists.',

            // Link validation messages
            'gcal.url' => 'Please provide a valid URL for Google Calendar.',

            // Librarian validation message
            'librarian_ids.*.exists' => 'One or more selected librarians do not exist.'
        ];
    }
}
