<?php

namespace App\Dto\Device;

class RegisterDeviceDto
{
    public function __construct(
        public readonly int $deviceModelId,
        public readonly string $accessKey,
        public readonly string $color,
        public readonly string $imei1,
        public readonly string $imei2,
    ) {}
}
