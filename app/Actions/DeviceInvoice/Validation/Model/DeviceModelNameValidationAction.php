<?php

namespace App\Actions\DeviceInvoice\Validation\Model;

use App\Actions\DeviceInvoice\Validation\Base\BaseDeviceProductValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\DeviceModel;
use App\Utils\StringNormalize;

class DeviceModelNameValidationAction extends BaseDeviceProductValidationAction
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
        return $this->device->deviceModel->name;
    }

    /**
     * Returns the minimum similarity ratio for this validation type.
     */
    protected function minSimilarityRatio(): int
    {
        return DeviceAttributeValidationRatio::MIN_MODEL_NAME_SIMILARITY;
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
        return 'model_name';
    }
}
