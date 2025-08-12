<?php

namespace App\Actions\Auth\Login;

use App\Actions\Validator\AuthValidator;
use App\Dto\Auth\LoginDto;
use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LoginAction
{
    private readonly ?User $user;

    public function __construct(
        private readonly string $email,
        private readonly string $password
    ) {}

    public function execute(): LoginDto
    {
        $this->initializeAttributesBeforeAction();
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
     * Initialize the user before the action occurs.
     */
    private function initializeAttributesBeforeAction(): void
    {
        $this->user = User::where('email', $this->email)->first();
    }

    /**
     * Validates the user's data before executing the action.
     */
    private function validateAttributesBeforeAction(): void
    {
        AuthValidator::for($this->user, $this->password)
            ->userMustBeExists()
            ->passwordMustBeTheUser();
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
     * Handles an exception that occurred during the login of a user.
     */
    private function handleException(Exception $e): never
    {
        $this->logError($e);
        $this->throwException();
    }

    /**
     * Logs an error message when a user login attempt fails.
     */
    private function logError(Exception $e): void
    {
        Log::error('User login failure.', [
            'errors' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    /**
     * Throws an exception when a login attempt fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.auth.errors.login'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
