<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Device\CreateDeviceTransferRequest;
use App\Http\Resources\DeviceTransfer\DeviceTransferResource;
use App\Models\Device;
use App\Models\DeviceTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceTransferController extends Controller
{
    /**
     * Create device transfer.
     */
    public function create(CreateDeviceTransferRequest $request, Device $device): JsonResponse
    {
        $request->user()
            ->deviceTransferService()
            ->create($request->targetUser(), $device);

        return response()->json(
            FlashMessage::success(trans('actions.device_transfer.success.create')),
            Response::HTTP_CREATED
        );
    }

    /**
     * Accept the device transfer.
     */
    public function acceptDeviceTransfer(Request $request, DeviceTransfer $deviceTransfer): Response
    {
        $this->authorize('acceptDeviceTransfer', $deviceTransfer);

        $currentUser = $request->user();
        $currentUser->acceptDeviceTransfer($deviceTransfer);

        return response()->json(
            FlashMessage::success(trans_choice('flash_messages.success.accepted.f', 1, [
                'model' => trans('model.device_transfer'),
            ]))->merge([
                'transfer' => new DeviceTransferResource($deviceTransfer),
            ]),
            Response::HTTP_OK
        );
    }

    /**
     * Reject the device transfer.
     */
    public function rejectDeviceTransfer(Request $request, DeviceTransfer $deviceTransfer): Response
    {
        $this->authorize('rejectDeviceTransfer', $deviceTransfer);

        $currentUser = $request->user();
        $currentUser->rejectDeviceTransfer($deviceTransfer);

        return response()->json(
            FlashMessage::success(trans_choice('flash_messages.success.rejected.f', 1, [
                'model' => trans('model.device_transfer'),
            ]))->merge([
                'transfer' => new DeviceTransferResource($deviceTransfer),
            ]),
            Response::HTTP_OK
        );
    }

    /**
     * Cancel the device transfer.
     */
    public function cancelDeviceTransfer(Request $request, DeviceTransfer $deviceTransfer): Response
    {
        $this->authorize('cancelDeviceTransfer', $deviceTransfer);

        $currentUser = $request->user();
        $currentUser->cancelDeviceTransfer($deviceTransfer);

        return response()->json(
            FlashMessage::success(trans_choice('flash_messages.success.canceled.f', 1, [
                'model' => trans('model.device_transfer'),
            ]))->merge([
                'transfer' => new DeviceTransferResource($deviceTransfer),
            ]),
            Response::HTTP_OK
        );
    }
}
