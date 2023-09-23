<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Resources\Device\DevicePublicResource;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeviceSharingController extends Controller
{
    /**
     * Generate device registration sharing link.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateSharingUrl(Request $request, Device $device): JsonResponse
    {
        if ($request->user()->cannot('generateSharingLink', $device)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $url = $device->generateSharingUrl();

        return response()->json(['url' => $url]);
    }

    /**
     * View device data.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Device\DeviceResource
     */
    public function viewDeviceSharedByUrl(Device $device): DevicePublicResource
    {
        return new DevicePublicResource($device);
    }
}
