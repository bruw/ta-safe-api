<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;

use App\Http\Requests\Device\ViewDeviceByTokenRequest;
use App\Http\Resources\Device\DevicePublicResource;

use App\Models\Device;
use App\Models\DeviceSharingToken;

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

    /**
     * TODO
     * 
     * @param \App\Http\Requests\Device\ViewDeviceByTokenRequest $request
     * @return \App\Http\Resources\Device\DevicePublicResource
     */
    public function viewDeviceByToken(ViewDeviceByTokenRequest $request): DevicePublicResource
    {
        $data = $request->validated();

        $deviceSharing = DeviceSharingToken::where([
            'token' => $data['token']
        ])->firstOrFail();

        $device = $deviceSharing->device;

        return new DevicePublicResource($device);
    }
}
