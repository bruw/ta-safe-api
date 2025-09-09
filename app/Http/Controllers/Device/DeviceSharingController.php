<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Device\ViewDeviceByTokenRequest;
use App\Http\Resources\Device\DevicePublicResource;
use App\Models\Device;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class DeviceSharingController extends Controller
{
    /**
     * Create a token to share device registration.
     */
    public function createSharingToken(Device $device): Response
    {
        $this->authorize('accessAsOwner', $device);

        $sharingToken = request()->user()
            ->deviceService()
            ->createSharingToken($device);

        return response()->json(FlashMessage::success(
            trans('actions.device.success.token'))->merge([
                'id' => $sharingToken->id,
                'token' => $sharingToken->token,
                'expires_at' => $sharingToken->expires_at,
            ]), Response::HTTP_CREATED
        );
    }

    /**
     * View device by sharing token.
     */
    public function viewDeviceByToken(ViewDeviceByTokenRequest $request): JsonResource
    {
        return new DevicePublicResource($request->deviceSharingToken()->device);
    }
}
