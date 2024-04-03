<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Device\RegisterDeviceRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{
    /**
     * Register new Device
     */
    public function registerDevice(RegisterDeviceRequest $request): Response
    {
        $data = $request->validated();
        $currentUser = $request->user();

        $currentUser->registerDevice($data);

        return response()->json(
            FlashMessage::success(trans_choice('flash_messages.success.registered.m', 1, [
                'model' => trans_choice('model.device', 1),
            ])),
            Response::HTTP_CREATED
        );
    }

    /**
     * View device data.
     */
    public function viewDevice(Request $request, Device $device): DeviceResource
    {
        $this->authorize('view', $device);

        return new DeviceResource($device);
    }
}
