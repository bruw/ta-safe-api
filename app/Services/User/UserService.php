<?php

namespace App\Services\User;

use App\Actions\User\Update\UpdateUserAction;
use App\Dto\User\UpdateUserDto;
use App\Models\User;

class UserService
{
    public function __construct(
        private readonly User $user
    ) {}

    /**
     * Updates the user with the given data.
     */
    public function update(UpdateUserDto $data): User
    {
        return (new UpdateUserAction($this->user, $data))->execute();
    }
}
