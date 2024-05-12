<?php

namespace App\Constants;

class DeviceAttributeValidationRatio
{
    /**
     * Name similarity must be equal to or greater than 75%.
     */
    public const MIN_NAME_SIMILARITY = 75;

    /**
     * Brand name similarity must be equal to or greater than 75%.
     */
    public const MIN_BRAND_SIMILARITY = 75;

    /**
     * Device model name similarity must be equal to or greater than 85%.
     */
    public const MIN_MODEL_NAME_SIMILARITY = 85;

    /**
     * Device color similarity must be equal to or greater than 70%.
     */
    public const MIN_COLOR_SIMILARITY = 70;

    /**
     * Device ram size similarity must be equal to or greater than 70%.
     */
    public const MIN_RAM_SIMILARITY = 70;

    /**
     * Device storage size similarity must be equal to or greater than 70%.
     */
    public const MIN_STORAGE_SIMILARITY = 70;

    /**
     * Device IMEIS similarity must be equal to or greater than 90%.
     */
    public const MIN_IMEI_SIMILARITY = 90;
}
