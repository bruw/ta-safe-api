<?php

namespace App\Enums\Device;

enum DeviceValidationStatus: string
{
    case PENDING = 'pending';
    case IN_ANALYSIS = 'in_analysis';
    case REJECTED = 'rejected';
    case VALIDATED = 'validated';

    /**
     * Retrieve an array of all the string values for each case in the enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if the device validation status is pending.
     */
    public function isPending(): bool
    {
        return $this->value === self::PENDING->value;
    }

    /**
     * Check if the device validation status is in analysis.
     */
    public function isInAnalysis(): bool
    {
        return $this->value === self::IN_ANALYSIS->value;
    }

    /**
     * Check if the device validation status is rejected.
     */
    public function isRejected(): bool
    {
        return $this->value === self::REJECTED->value;
    }

    /**
     * Check if the device validation status is validated.
     */
    public function isValidated(): bool
    {
        return $this->value === self::VALIDATED->value;
    }

}
