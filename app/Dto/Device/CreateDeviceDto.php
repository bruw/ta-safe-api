<?php

namespace App\Dto\Device;

use App\Http\Requests\Device\Create\CreateDeviceRequest;

class CreateDeviceDto
{
    public function __construct(
        public readonly int $deviceModelId,
        public readonly string $accessKey,
        public readonly string $color,
        public readonly string $imei1,
        public readonly string $imei2
    ) {}

    public static function for(CreateDeviceRequest $request): self
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
