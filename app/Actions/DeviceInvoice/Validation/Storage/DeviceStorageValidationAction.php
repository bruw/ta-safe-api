<?php

namespace App\Actions\DeviceInvoice\Validation\Storage;

use App\Actions\DeviceInvoice\Validation\Base\BaseDeviceProductValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\DeviceModel;
use App\Utils\StringNormalize;

class DeviceStorageValidationAction extends BaseDeviceProductValidationAction
{
    /**
     * Returns the given value normalized.
     */
    protected function normalize(string $value): string
    {
        return StringNormalize::for($value)
            ->removeExtraWhiteSpaces()
            ->normalizeMemorySize()
            ->get();
    }

    /**
     * Returns the device attribute to validate.
     */
    protected function deviceAttributeToValidate(): string
    {
        return $this->device->deviceModel->storage;
    }

    /**
     * Returns the minimum similarity ratio for this validation type.
     */
    protected function minSimilarityRatio(): int
    {
        return DeviceAttributeValidationRatio::MIN_STORAGE_SIMILARITY;
    }

    /**
     * Returns the source of the attribute to validate.
     */
    protected function attributeSource(): string
    {
        return DeviceModel::class;
    }

    /**
     * Returns the label of the attribute to validate.
     */
    protected function attributeLabel(): string
    {
        return 'storage';
    }
}
