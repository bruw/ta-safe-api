<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiFormRequest;
use App\Models\User;

class SearchUserRequest extends ApiFormRequest
{
    /**
     * Validate the 'email' field and return the user linked to it.
     */
    public function userByEmail(): User
    {
        return User::where('email', $this->email)->firstOrFail();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'bail',
                'required',
                'email',
                'max:255',
                'exists:users,email',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.exists' => trans('validation.custom.email.exists'),
        ];
    }
}
