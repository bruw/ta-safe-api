<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\RegisterDeviceRequest;
use App\Http\Requests\Device\TransferDeviceRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use App\Models\User;
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
        $this->authorize('view', $device);

        return new DeviceResource($device);
    }

    /**
     * Transfer a device to a user.
     * 
     * @param \App\Http\Requests\Device\TransferDeviceRequest $request
     * @param \App\Models\Device $device
     * @return \Illuminate\Http\Response
     */
    public function transferDevice(TransferDeviceRequest $request, Device $device): Response
    {
        $data = $request->validated();

        $currentUser = $request->user();

        $targetUser = User::where([
            'id' => $data['target_user_id']
        ])->firstOrFail();

        $currentUser->transferDevice($targetUser, $device);

        return response()->noContent(Response::HTTP_CREATED);
    }
}
