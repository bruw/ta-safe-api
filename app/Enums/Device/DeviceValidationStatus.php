<?php

namespace App\Enums\Device;

enum DeviceValidationStatus: string
{
    case PENDING = 'pending';
    case IN_ANALYSIS = 'in_analysis';
    case REJECTED = 'rejected';
    case VALIDATED = 'validated';

    public function isPending(): bool
    {
        return $this->value === self::PENDING->value;
    }

    public function isInAnalysis(): bool
    {
        return $this->value === self::IN_ANALYSIS->value;
    }

    public function isRejected(): bool
    {
        return $this->value === self::REJECTED->value;
    }

    public function isValidated(): bool
    {
        return $this->value === self::VALIDATED->value;
    }
}
