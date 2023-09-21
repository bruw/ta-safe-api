<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\RegisterDeviceRequest;

use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{
    /**
     * Register new Device
     * 
     * @param App\Http\Requests\Device\RegisterDeviceRequest $request
     * @return \Illuminate\Http\Response
     */
    public function registerDevice(RegisterDeviceRequest $request): Response
    {
        $data = $request->validated();
        $currentUser = $request->user();

        $currentUser->registerDevice($data);

        return response()->noContent(Response::HTTP_CREATED);
    }
}
