<?php

namespace App\Enums\Device;

enum DeviceTransferStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
    case ACCEPTED = 'accepted';

    public function isPending(): bool
    {
        return $this->value === self::PENDING->value;
    }

    public function isRejected(): bool
    {
        return $this->value === self::REJECTED->value;
    }

    public function isCanceled(): bool
    {
        return $this->value === self::CANCELED->value;
    }

    public function isAccepted(): bool
    {
        return $this->value === self::ACCEPTED->value;
    }
}
