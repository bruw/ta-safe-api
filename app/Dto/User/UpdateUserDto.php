<?php

namespace App\Dto\User;

class UpdateUserDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
    ) {}
}
