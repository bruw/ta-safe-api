<?php

namespace App\Actions\DeviceInvoice\Validation\Device\Color;

use App\Actions\DeviceInvoice\Validation\Device\Base\BaseDeviceProductValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Utils\StringNormalize;

class DeviceColorValidationAction extends BaseDeviceProductValidationAction
{
    /**
     * Returns the given value normalized.
     */
    protected function normalize(string $value): string
    {
        return StringNormalize::for($value)
            ->removeAccents()
            ->removeNonAlphanumeric()
            ->removeExtraWhiteSpaces()
            ->toLowerCase()
            ->get();
    }

    /**
     * Returns the device attribute to validate.
     */
    protected function deviceAttributeToValidate(): string
    {
        return $this->device->color;
    }

    /**
     * Returns the minimum similarity ratio for this validation type.
     */
    protected function minSimilarityRatio(): int
    {
        return DeviceAttributeValidationRatio::MIN_COLOR_SIMILARITY;
    }

    /**
     * Returns the source of the attribute to validate.
     */
    protected function attributeSource(): string
    {
        return Device::class;
    }

    /**
     * Returns the label of the attribute to validate.
     */
    protected function attributeLabel(): string
    {
        return 'color';
    }
}
