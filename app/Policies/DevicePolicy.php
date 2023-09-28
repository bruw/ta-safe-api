<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;

class DevicePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Device $device): bool
    {
        return $user->id == $device->user->id;
    }

    /**
     * Determine whether the user can generate the device registration sharing link.
     */
    public function generateSharingLink(User $user, Device $device): bool
    {
        return $user->id == $device->user->id;
    }

    /**
     * Determine whether the user can create device transfer.
     */
    public function createDeviceTransfer(User $user, Device $device): bool
    {
        return $user->id == $device->user->id;
    }
}
