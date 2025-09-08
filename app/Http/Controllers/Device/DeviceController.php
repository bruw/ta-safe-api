<?php

namespace App\Http\Controllers\Device;

use App\Dto\Device\RegisterDeviceDto;
use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Device\RegisterDeviceRequest;
use App\Http\Requests\Device\StartDeviceValidationRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{
    /**
     * View device data.
     */
    public function view(Device $device): JsonResource
    {
        $this->authorize('accessAsOwner', $device);

        $device->loadMissing([
            'attributeValidationLogs',
            'transfers' => fn ($q) => $q->acceptedAndOrdered(),
        ]);

        return new DeviceResource($device);
    }

    /**
     * Register a new device.
     */
    public function register(RegisterDeviceRequest $request): JsonResponse
    {
        $request->user()
            ->deviceService()
            ->register(RegisterDeviceDto::fromRequest($request));

        return response()->json(FlashMessage::success(
            trans('actions.device.success.register')),
            Response::HTTP_CREATED,
        );
    }

    /**
     * Delete a device with rejected validation.
     */
    public function delete(Device $device): Response
    {
        $this->authorize('accessAsOwner', $device);

        request()->user()
            ->deviceService()
            ->delete($device);

        return response()->json(FlashMessage::success(
            trans('actions.device.success.delete')),
            Response::HTTP_OK
        );
    }

    /**
     * Validate a device's registration.
     */
    public function validation(StartDeviceValidationRequest $request, Device $device): JsonResponse
    {
        $request->user()
            ->deviceService()
            ->validate($device, $request->invoiceData());

        return response()->json(FlashMessage::success(
            trans('actions.device.success.validate'))->merge([
                'device' => new DeviceResource($device),
            ]), Response::HTTP_OK
        );
    }

    /**
     * Invalidate a device's registration.
     */
    public function invalidation(Device $device): JsonResponse
    {
        $this->authorize('accessAsOwner', $device);

        request()->user()
            ->deviceService()
            ->invalidate($device);

        return response()->json(FlashMessage::success(
            trans('actions.device.success.invalidate'))->merge([
                'device' => new DeviceResource($device),
            ]), Response::HTTP_OK
        );
    }
}
