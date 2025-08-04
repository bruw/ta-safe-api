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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'max:255', Rules\Password::defaults()],
            'cpf' => [
                'required',
                'unique:users,cpf',
                'regex:/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/',
                new CpfRule,
            ],
            'phone' => [
                'required',
                'unique:users,phone',
                'regex:/^[(]\d{2}[)]\s\d{5}-\d{4}$/',
            ],
        ];
    }
}
