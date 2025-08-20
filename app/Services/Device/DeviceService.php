<?php

namespace App\Services\Device;

use App\Actions\Device\Delete\DeleteDeviceAction;
use App\Actions\Device\Register\RegisterDeviceAction;
use App\Actions\Device\Token\CreateSharingTokenAction;
use App\Dto\Device\RegisterDeviceDto;
use App\Models\Device;
use App\Models\DeviceSharingToken;
use App\Models\User;

class DeviceService
{
    public function __construct(
        private readonly User $user
    ) {}

    /**
     * Registers a device for the given user.
     */
    public function register(RegisterDeviceDto $data): Device
    {
        return (new RegisterDeviceAction($this->user, $data))->execute();
    }

    /**
     * Deletes a device for the given user.
     */
    public function delete(Device $device): bool
    {
        return (new DeleteDeviceAction($this->user, $device))->execute();
    }

    /**
     * Creates a sharing token for the given device.
     */
    public function createSharingToken(Device $device): DeviceSharingToken
    {
        return (new CreateSharingTokenAction($this->user, $device))->execute();
    }
}
