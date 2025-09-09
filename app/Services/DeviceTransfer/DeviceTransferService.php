<?php

namespace App\Services\DeviceTransfer;

use App\Actions\DeviceTransfer\Accept\AcceptDeviceTransferAction;
use App\Actions\DeviceTransfer\Cancel\CancelDeviceTransferAction;
use App\Actions\DeviceTransfer\Create\CreateDeviceTransferAction;
use App\Actions\DeviceTransfer\Reject\RejectDeviceTransferAction;
use App\Models\Device;
use App\Models\DeviceTransfer;
use App\Models\User;

class DeviceTransferService
{
    public function __construct(
        private readonly User $user
    ) {}

    /**
     * Create a new device transfer.
     */
    public function create(User $targetUser, Device $device): DeviceTransfer
    {
        return (new CreateDeviceTransferAction($this->user, $targetUser, $device))->execute();
    }

    /**
     * Accept a device transfer.
     */
    public function accept(DeviceTransfer $transfer): DeviceTransfer
    {
        return (new AcceptDeviceTransferAction($this->user, $transfer))->execute();
    }

    /**
     * Cancel a device transfer.
     */
    public function cancel(DeviceTransfer $transfer): DeviceTransfer
    {
        return (new CancelDeviceTransferAction($this->user, $transfer))->execute();
    }

    /**
     * Reject a device transfer.
     */
    public function reject(DeviceTransfer $transfer): DeviceTransfer
    {
        return (new RejectDeviceTransferAction($this->user, $transfer))->execute();
    }
}
