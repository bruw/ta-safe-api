<?php

namespace App\Services\Auth;

use App\Actions\Auth\Login\LoginAction;
use App\Actions\Auth\Register\RegisterUserAction;
use App\Dto\Auth\LoginDto;
use App\Dto\Auth\RegisterUserDto;

class AuthService
{
    /**
     * Logs the user in and returns a LoginDto containing the user
     * and the Personal Access Token.
     */
    public function login(string $email, string $password): LoginDto
    {
        return (new LoginAction($email, $password))->execute();
    }

    /**
     * Registers a new user with the given data and returns a LoginDto
     * containing the user and the Personal Access Token.
     */
    public function register(RegisterUserDto $data): LoginDto
    {
        return (new RegisterUserAction($data))->execute();
    }
}
