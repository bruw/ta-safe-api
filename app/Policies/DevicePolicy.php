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
     * Determine whether the user can create a device sharing token.
     */
    public function createSharingToken(User $user, Device $device): bool
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

    /**
     *  Determine whether the user can validate the registration of a device.
     */
    public function validateDeviceRegistration(User $user, Device $device): bool
    {
        return $user->id == $device->user->id;
    }

    /**
     *  Determine whether the user can invalidate the registration of a device.
     */
    public function invalidateDeviceRegistration(User $user, Device $device): bool
    {
        return $user->id == $device->user->id;
    }
}
