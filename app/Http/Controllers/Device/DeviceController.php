<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\RegisterDeviceRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{
    /**
     * Register new Device
     * 
     * @param \App\Http\Requests\Device\RegisterDeviceRequest $request
     * @return \Illuminate\Http\Response
     */
    public function registerDevice(RegisterDeviceRequest $request): Response
    {
        $data = $request->validated();
        $currentUser = $request->user();

        $currentUser->registerDevice($data);

        return response()->noContent(Response::HTTP_CREATED);
    }

    /**
     * View device data.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Device\DeviceResource
     */
    public function viewDevice(Request $request, Device $device): DeviceResource
    {
        if ($request->user()->cannot('view', $device)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return new DeviceResource($device);
    }
}
