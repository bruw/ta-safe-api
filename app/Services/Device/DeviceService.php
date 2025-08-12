<?php

namespace App\Services\Device;

use App\Actions\Device\Register\RegisterDeviceAction;
use App\Dto\Device\RegisterDeviceDto;
use App\Models\Device;
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
}
