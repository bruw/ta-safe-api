<?php

namespace App\Enums\Device;

enum DeviceValidationStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case VALIDATED = 'validated';
}
