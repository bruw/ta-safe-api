<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use App\Rules\CpfRule;
use Illuminate\Validation\Rules;

class RegisterUserRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
                'bail',
                'required',
                'email',
                'unique:users,email',
            ],
            'cpf' => [
                'bail',
                'required',
                'regex:/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/',
                new CpfRule,
                'unique:users',
            ],
            'phone' => [
                'bail',
                'required',
                'regex:/^[(]\d{2}[)]\s\d{5}-\d{4}$/',
                'unique:users,phone',
            ],
            'password' => [
                'required',
                'confirmed',
                'max:255',
                Rules\Password::defaults(),
            ],
        ];
    }
}
