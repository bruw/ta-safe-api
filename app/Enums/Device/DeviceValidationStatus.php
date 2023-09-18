<?php

namespace App\Enums\Device;

enum DeviceValidationStatus
{
    case PENDING;
    case REJECTED;
    case VALIDATED;
    case FAIL;
}
