<?php

namespace App\Dto\Auth;

use App\Models\User;

class LoginDto
{
    public function __construct(
        public readonly User $user,
        public readonly string $token
    ) {}
}
