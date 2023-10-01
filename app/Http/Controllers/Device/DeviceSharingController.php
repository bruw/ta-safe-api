<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;

class DeviceSharingController extends Controller
{
    /**
     * Create token to share device registration.
     * 
     * @param \App\Models\Device $device
     * @return \App\Http\Resources\Device\DeviceResource
     */
    public function createSharingToken(Device $device): DeviceResource
    {
        $this->authorize('createSharingToken', $device);
        $device->createSharingToken();

        $device->refresh();

        return new DeviceResource($device);
    }
}
