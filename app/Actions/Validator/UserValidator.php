<?php

namespace App\Actions\Validator;

use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserValidator
{
    public function __construct(
        private readonly User $user
    ) {}

    /**
     * Creates a new instance of UserValidator for the specified user.
     */
    public static function for(User $user): self
    {
        return new self($user);
    }

    /**
     * Validates if the given password matches the user's current password.
     */
    public function passwordMustBeTheUser(string $password): self
    {
        $isSamePassword = Hash::check($password, $this->user->password);

        throw_unless($isSamePassword, new HttpJsonResponseException(
            trans('auth.password'),
            Response::HTTP_UNAUTHORIZED
        ));

        return $this;
    }
}
