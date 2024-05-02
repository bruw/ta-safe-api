<?php

namespace App\Enums\Device;

enum DeviceValidationStatus: string
{
    case PENDING = 'pending';
    case IN_ANALYSIS = 'in_analysis';
    case REJECTED = 'rejected';
    case VALIDATED = 'validated';
}
