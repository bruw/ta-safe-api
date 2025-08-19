<?php

namespace App\Policies;

use App\Models\DeviceTransfer;
use App\Models\User;

class DeviceTransferPolicy
{
    /**
     * Determines whether the user is the target user of the device transfer.
     */
    public function accessAsTargetUser(User $user, DeviceTransfer $deviceTransfer): bool
    {
        return $user->id == $deviceTransfer->target_user_id;
    }

    /**
     * Determines whether the user is the source user of the device transfer.
     */
    public function accessAsSourceUser(User $user, DeviceTransfer $deviceTransfer): bool
    {
        return $user->id == $deviceTransfer->source_user_id;
    }

    /**
     * determines whether the user can reject the device transfer.
     */
    public function rejectDeviceTransfer(User $user, DeviceTransfer $deviceTransfer): bool
    {
        return $user->id == $deviceTransfer->target_user_id;
    }
}
