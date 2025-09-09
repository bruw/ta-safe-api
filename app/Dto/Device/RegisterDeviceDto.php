<?php

namespace App\Dto\Device;

use App\Http\Requests\Device\RegisterDeviceRequest;

class RegisterDeviceDto
{
    public function __construct(
        public readonly int $deviceModelId,
        public readonly string $accessKey,
        public readonly string $color,
        public readonly string $imei1,
        public readonly string $imei2,
    ) {}

    /**
     * Creates a new RegisterDeviceDto instance from the given request.
     */
    public static function fromRequest(RegisterDeviceRequest $request): self
    {
        return new self(
            deviceModelId: $request->device_model_id,
            accessKey: $request->access_key,
            color: $request->color,
            imei1: $request->imei_1,
            imei2: $request->imei_2,
        );
    }
}
