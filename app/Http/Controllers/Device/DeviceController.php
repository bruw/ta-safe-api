<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Device\DeviceRegistrationValidationRequest;
use App\Http\Requests\Device\RegisterDeviceRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Resources\Json\JsonResource;
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
    public function viewDevice(Device $device): JsonResource
    {
        $this->authorize('view', $device);

        return new DeviceResource($device);
    }

    /**
     * TODO DESCRIPTION
     */
    public function registrationValidation(DeviceRegistrationValidationRequest $request, Device $device)
    {
        $data = $request->validated();

        $device->registrationValidation(
            cpf: $data['cpf'],
            name: $data['name'],
            products: $data['products']
        );

        //TODO FLASH MESSAGE
        return response()->json(
            FlashMessage::success("Validação realiza com sucesso!"),
            Response::HTTP_CREATED
        );
    }
}
