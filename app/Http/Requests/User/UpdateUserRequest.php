<?php

namespace App\Http\Requests\User;

use App\Dto\User\UpdateUserDto;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiFormRequest
{
    /**
     * Validate fields and convert the request into a UpdateUserDto instance.
     */
    public function toDto(): UpdateUserDto
    {
        return new UpdateUserDto(
            name: $this->name,
            email: $this->email,
            phone: $this->phone,
        );
    }

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
                'bail',
                'required',
                'email',
                'max:255',
                Rule::unique('users')
                    ->ignore($this->user()->id),
            ],
            'phone' => [
                'bail',
                'required',
                'regex:/^[(]\d{2}[)]\s\d{5}-\d{4}$/',
                Rule::unique('users')
                    ->ignore($this->user()->id),
            ],
        ];
    }
}
