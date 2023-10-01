<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Response;

class DeviceSharingController extends Controller
{
    /**
     * Create token to share device registration.
     * 
     * @param \App\Models\Device $device
     * @return \Illuminate\Http\Response
     */
    public function createSharingToken(Device $device): Response
    {
        $this->authorize('createSharingToken', $device);
        $device->createSharingToken();

        return response()->noContent(Response::HTTP_CREATED);
    }
}
