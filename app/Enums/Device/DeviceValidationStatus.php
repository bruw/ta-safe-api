<?php

namespace App\Enums\Device;

enum DeviceValidationStatus: string
{
    case WAITING = 'waiting';
    case REFUSED = 'refused';
    case VALIDATED = 'validated';
    case FAIL = 'fail';
}
