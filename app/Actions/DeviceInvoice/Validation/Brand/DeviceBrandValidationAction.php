<?php

namespace App\Actions\DeviceInvoice\Validation\Brand;

use App\Actions\DeviceInvoice\Validation\Base\BaseDeviceProductValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Brand;

class DeviceBrandValidationAction extends BaseDeviceProductValidationAction
{
    /**
     * Returns the given value normalized.
     */
    protected function normalize(string $value): string
    {
        return $this->extractOnlyLetters($value);
    }

    /**
     * Returns the device attribute to validate.
     */
    protected function deviceAttributeToValidate(): string
    {
        return $this->device->deviceModel->brand->name;
    }

    /**
     * Returns the minimum similarity ratio for this validation type.
     */
    protected function minSimilarityRatio(): int
    {
        return DeviceAttributeValidationRatio::MIN_BRAND_SIMILARITY;
    }

    /**
     * Returns the source of the attribute to validate.
     */
    protected function attributeSource(): string
    {
        return Brand::class;
    }

    /**
     * Returns the label of the attribute to validate.
     */
    protected function attributeLabel(): string
    {
        return 'brand_name';
    }
}
