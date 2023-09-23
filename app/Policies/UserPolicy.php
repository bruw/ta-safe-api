<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the current user can obtain the devices of the informed user.
     */
    public function getDevices(User $currentUser, User $user): bool
    {
        return $currentUser->id == $user->id;
    }
}
