<?php

namespace App\Enums\Device;

enum DeviceTransferStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
    case ACCEPTED = 'accepted';
}
