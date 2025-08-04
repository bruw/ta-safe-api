<?php

namespace App\Actions\Auth\Login;

use App\Actions\Validator\UserValidator;
use App\Dto\Auth\LoginDto;
use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LoginAction
{
    public function __construct(
        private readonly User $user,
        private readonly string $password
    ) {}

    public function execute(): LoginDto
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->deleteAllTokens();
                $token = $this->createToken();
                $this->logSuccess($this->user);

                return new LoginDto($this->user, $token);
            });
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Validates the user's password before executing the action.
     */
    private function validateAttributesBeforeAction(): void
    {
        UserValidator::for($this->user)
            ->passwordMustBeTheUser($this->password);
    }

    /**
     * Deletes all authentication tokens for the user.
     */
    private function deleteAllTokens(): void
    {
        $this->user->tokens()->delete();
    }

    /**
     * Creates a new authentication token for the current user.
     */
    private function createToken(): string
    {
        return $this->user->createToken('auth-token')->plainTextToken;
    }

    /**
     * Logs a success message when a new login is effected.
     */
    private function logSuccess(User $user): void
    {
        Log::info('User successfully logged in.', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Handles exceptions that occur during a user login.
     */
    private function handleException(Exception $e): void
    {
        Log::error('User login failure.', [
            'errors' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);

        throw new HttpJsonResponseException(
            trans('actions.auth.errors.login'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
