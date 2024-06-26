<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Device\RegisterDeviceRequest;
use App\Http\Requests\Device\ValidateDeviceRegistrationRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Jobs\Device\ValidateDeviceRegistrationJob;
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
     * Delete a device with rejected validation.
     */
    public function deleteDevice(Device $device): Response
    {
        $this->authorize('delete', $device);

        $device->safeDelete();

        return response()->json(
            FlashMessage::success(trans('actions.device.deleted')),
            Response::HTTP_OK
        );
    }

    /**
     * View device data.
     */
    public function viewDevice(Device $device): JsonResource
    {
        $this->authorize('view', $device);

        $device->setAttribute(
            'validation_attributes',
            $device->validatedAttributes()
        );

        $device->setAttribute(
            'transfers_history',
            $device->transfers()->acceptedAndOrdered()->get()
        );

        return new DeviceResource($device);
    }

    /**
     * Validating the registration of a device.
     */
    public function validateRegistration(ValidateDeviceRegistrationRequest $request, Device $device)
    {
        $data = $request->validated();

        $device->validateRegistration(
            cpf: $data['cpf'],
            name: $data['name'],
            products: $data['products']
        );

        ValidateDeviceRegistrationJob::dispatchAfterResponse($device);

        return response()->json(
            FlashMessage::success(trans('actions.device_validation.start'))->merge([
                'device' => new DeviceResource($device),
            ]),
            Response::HTTP_OK
        );
    }

    /**
     * Invalidate a device's registration.
     */
    public function invalidateRegistration(Device $device): Response
    {
        $this->authorize('invalidateDeviceRegistration', $device);

        $invalidated = $device->invalidateRegistration();

        if ($invalidated) {
            return response()->json(
                FlashMessage::success(trans('actions.device_validation.invalid'))->merge([
                    'device' => new DeviceResource($device),
                ]),
                Response::HTTP_OK
            );
        }

        return response()->noContent(Response::HTTP_OK);
    }
}
