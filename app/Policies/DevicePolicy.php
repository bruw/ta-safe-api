<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;

class DevicePolicy
{
    /**
     * Determine whether the user can access the given device as owner.
     */
    public function accessAsOwner(User $user, Device $device): bool
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
