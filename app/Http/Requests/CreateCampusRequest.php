<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCampusRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:' . self::NAME_MAX_LENGTH,
                Rule::unique('campuses', 'name')
            ],
            'code' => [
                'required',
                'string',
                'max:' . self::CODE_MAX_LENGTH,
                Rule::unique('campuses', 'code')
            ],
            'gcal' => [
                'nullable',
                'url',
                'max:' . self::GCAL_MAX_LENGTH
            ],
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
            'name.unique' => 'A campus with this name already exists.',
            'code.unique' => 'A campus with this code already exists.',
            'gcal.url' => 'Please provide a valid URL for Google Calendar.',
            'librarian_ids.*.exists' => 'One or more selected librarians do not exist.'
        ];
    }
}
