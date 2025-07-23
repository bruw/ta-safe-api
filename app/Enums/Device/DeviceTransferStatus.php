<?php

namespace App\Enums\Device;

enum DeviceTransferStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
    case ACCEPTED = 'accepted';

    /**
     * Retrieve an array of all the string values for each case in the enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if the transfer status is pending.
     */
    public function isPending(): bool
    {
        return $this->value === self::PENDING->value;
    }

    /**
     * Check if the transfer status is rejected.
     */
    public function isRejected(): bool
    {
        return $this->value === self::REJECTED->value;
    }

    /**
     * Check if the transfer status is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->value === self::CANCELED->value;
    }

    /**
     * Check if the transfer status is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->value === self::ACCEPTED->value;
    }
}
