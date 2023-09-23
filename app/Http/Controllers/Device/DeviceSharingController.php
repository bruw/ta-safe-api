<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Resources\Device\DevicePublicResource;
use App\Models\Device;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class DeviceSharingController extends Controller
{
    /**
     * Generate device registration sharing link.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Device\DeviceResource
     */
    public function generateSharingLink(Request $request, Device $device)
    {
        if ($request->user()->cannot('generateSharingLink', $device)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return URL::temporarySignedRoute('share.device', now()->addHour(), [
            'device' => $device
        ]);
    }

    /**
     * View device data.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Device\DeviceResource
     */
    public function viewDeviceSharedByLink(Device $device): DevicePublicResource
    {
        return new DevicePublicResource($device);
    }
}
