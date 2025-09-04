<?php

namespace App\Actions\DeviceInvoice\Validation\Imei;

use App\Actions\DeviceInvoice\Validation\Base\BaseDeviceProductValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Utils\StringNormalize;

class DeviceImei2ValidationAction extends BaseDeviceProductValidationAction
{
    /**
     * Returns the given value normalized.
     */
    protected function normalize(string $value): string
    {
        return StringNormalize::for($value)
            ->keepOnlyDigits()
            ->removeExtraWhiteSpaces()
            ->get();
    }

    /**
     * Returns the device attribute to validate.
     */
    protected function deviceAttributeToValidate(): string
    {
        return $this->device->imei_2;
    }

    /**
     * Returns the minimum similarity ratio for this validation type.
     */
    protected function minSimilarityRatio(): int
    {
        return DeviceAttributeValidationRatio::MIN_IMEI_SIMILARITY;
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
        return 'imei_2';
    }
}
