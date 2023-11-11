<?php

namespace App\Policies;

use App\Models\DeviceTransfer;
use App\Models\User;

class DeviceTransferPolicy
{
    /**
     * determines whether the user can accept the device transfer.
     */
    public function acceptDeviceTransfer(User $user, DeviceTransfer $deviceTransfer): bool
    {
        return $user->id == $deviceTransfer->target_user_id;
    }

    /**
     * determines whether the user can reject the device transfer.
     */
    public function rejectDeviceTransfer(User $user, DeviceTransfer $deviceTransfer): bool
    {
        return $user->id == $deviceTransfer->target_user_id;
    }

    /**
     * determines whether the user can cancel the device transfer.
     */
    public function cancelDeviceTransfer(User $user, DeviceTransfer $deviceTransfer): bool
    {
        return $user->id == $deviceTransfer->source_user_id;
    }
}
