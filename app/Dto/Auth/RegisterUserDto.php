<?php

namespace App\Dto\Auth;

use App\Http\Requests\Auth\RegisterUserRequest;

class RegisterUserDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $cpf,
        public readonly string $phone,
        public readonly string $password
    ) {}

    public static function fromRequest(RegisterUserRequest $request): self
    {
        return new self(
            name: $request->name,
            email: $request->email,
            cpf: $request->cpf,
            phone: $request->phone,
            password: $request->password,
        );
    }
}
