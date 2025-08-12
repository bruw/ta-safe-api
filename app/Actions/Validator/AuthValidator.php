<?php

namespace App\Actions\Validator;

use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthValidator
{
    public function __construct(
        private readonly ?User $user,
        private readonly string $password
    ) {}

    /**
     * Creates a new instance of AuthValidator for the specified user and password.
     */
    public static function for(?User $user, string $password): self
    {
        return new self($user, $password);
    }

    /**
     * Validates if the given email exists in the database.
     */
    public function userMustBeExists(): self
    {
        throw_if(is_null($this->user), new HttpJsonResponseException(
            trans('auth.failed'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validates if the given password matches the user's current password.
     */
    public function passwordMustBeTheUser(): self
    {
        $isSamePassword = Hash::check($this->password, $this->user?->password);

        throw_unless($isSamePassword, new HttpJsonResponseException(
            trans('auth.failed'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }
}
