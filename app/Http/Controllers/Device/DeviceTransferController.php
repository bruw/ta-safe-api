<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\CreateDeviceTransferRequest;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Response;

class DeviceTransferController extends Controller
{
    /**
     * Create device transfer.
     * 
     * @param \App\Http\Requests\Device\CreateDeviceTransferRequest $request
     * @param \App\Models\Device $device
     * @return \Illuminate\Http\Response
     */
    public function createDeviceTransfer(CreateDeviceTransferRequest $request, Device $device): Response
    {
        $data = $request->validated();

        $currentUser = $request->user();

        $targetUser = User::where([
            'id' => $data['target_user_id']
        ])->firstOrFail();

        $currentUser->createDeviceTransfer($targetUser, $device);

        return response()->noContent(Response::HTTP_CREATED);
    }
}
