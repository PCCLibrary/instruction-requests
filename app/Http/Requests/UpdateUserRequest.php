<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;


class UpdateUserRequest extends FormRequest
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
    public function rules()
    {
        $id = $this->route('user');

        Log::info('Update User ID: ' . $id);

        $rules = [
            'name'     => 'required',
            'display_name' => 'required',
            'email'    => 'required|email|unique:users,email,'.$id,
            'campus_id' => 'nullable|exists:campuses,id',
            'is_scheduler' => 'boolean',
        ];

        return $rules;
    }
}
