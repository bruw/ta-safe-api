<?php

namespace App\Actions\Auth\Register;

use App\Dto\Auth\LoginDto;
use App\Dto\Auth\RegisterUserDto;
use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserAction
{
    public function __construct(
        private RegisterUserDto $data
    ) {}

    public function execute(): LoginDto
    {
        try {
            return DB::transaction(function () {
                $user = $this->register();
                $token = $this->createToken($user);
                $this->logSuccess($user);

                return new LoginDto($user, $token);
            });
        } catch (Exception $e) {
            $this->logError($e);
            $this->throwException();
        }
    }

    /**
     * Register a new user based on the '$data' provided.
     */
    private function register(): User
    {
        return User::create([
            'name' => $this->data->name,
            'email' => $this->data->email,
            'cpf' => $this->data->cpf,
            'phone' => $this->data->phone,
            'password' => Hash::make($this->data->password),
        ]);
    }

    /**
     * Generates a new token for the given user.
     */
    private function createToken(User $user): string
    {
        return $user->createToken('auth-token')->plainTextToken;
    }

    /**
     * Logs a success message when a new user is registered.
     */
    private function logSuccess(User $user): void
    {
        Log::info('User successfully registered.', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Logs an error message when a new user registration fails.
     */
    private function logError(Exception $e): void
    {
        Log::error('User registration failure.', [
            'cpf' => $this->data->cpf,
            'email' => $this->data->email,
            'errors' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    /**
     * Throws an exception when a registration attempt fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.auth.errors.register'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
