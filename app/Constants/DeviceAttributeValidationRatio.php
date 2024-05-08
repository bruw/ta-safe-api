<?php

namespace App\Constants;

class DeviceAttributeValidationRatio
{
    /**
     * CPF must be identical to be considered valid.
     */
    public const MIN_CPF_SIMILARITY = 100;

    /**
     * Name similarity must be equal to or greater than 75%.
     */
    public const MIN_NAME_SIMILARITY = 75;

    /**
     * Brand name similarity must be equal to or greater than 90%.
     */
    public const MIN_BRAND_SIMILARITY = 90;
}
