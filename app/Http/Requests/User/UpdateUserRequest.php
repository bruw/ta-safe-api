<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')
                    ->ignore($this->user()->id),
            ],
            'phone' => [
                'required',
                'regex:/^[(]\d{2}[)]\s\d{5}-\d{4}$/',
                Rule::unique('users')
                    ->ignore($this->user()->id),
            ],
        ];
    }
}
