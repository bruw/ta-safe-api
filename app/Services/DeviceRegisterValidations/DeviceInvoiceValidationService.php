<?php

namespace App\Services\DeviceRegisterValidations;

use App\Actions\DeviceInvoiceProductValidation\DeviceBrandValidationAction;
use App\Actions\DeviceInvoiceProductValidation\DeviceColorValidationAction;
use App\Actions\DeviceInvoiceProductValidation\DeviceImei1ValidationAction;
use App\Actions\DeviceInvoiceProductValidation\DeviceImei2ValidationAction;
use App\Actions\DeviceInvoiceProductValidation\DeviceModelNameValidationAction;
use App\Actions\DeviceInvoiceProductValidation\DeviceRamValidationAction;
use App\Actions\DeviceInvoiceProductValidation\DeviceStorageValidationAction;
use App\Actions\DeviceInvoiceProductValidation\FindProductInInvoiceMatchingDeviceAction;
use App\Actions\DeviceOwnerInvoiceValidation\DeviceOwnerCpfValidationAction;
use App\Actions\DeviceOwnerInvoiceValidation\DeviceOwnerNameValidationAction;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;

class DeviceInvoiceValidationService
{
    private string $productDescription;

    public function __construct(private Device $device)
    {
        $findProductAction = new FindProductInInvoiceMatchingDeviceAction($device);
        $this->productDescription = $findProductAction->execute();
    }

    /**
     * Validate that the invoice's cpf is the same as the user's.
     */
    public function validateOwnerCpf(): DeviceAttributeValidationLog
    {
        $cpfAction = new DeviceOwnerCpfValidationAction($this->device);

        return $cpfAction->execute();
    }

    /**
     * validate that the consumer name on the invoice is the same as the user name.
     */
    public function validateOwnerName(): DeviceAttributeValidationLog
    {
        $nameAction = new DeviceOwnerNameValidationAction($this->device);

        return $nameAction->execute();
    }

    /**
     * Validate that the brand name of the device on the invoice is the same as that on the registration.
     */
    public function validateBrand(): DeviceAttributeValidationLog
    {
        $brandAction = new DeviceBrandValidationAction($this->device, $this->productDescription);

        return $brandAction->execute();
    }

    /**
     * Validate that the model name of the device on the invoice is the same as the one in the register.
     */
    public function validateModel(): DeviceAttributeValidationLog
    {
        $deviceModelAction = new DeviceModelNameValidationAction($this->device, $this->productDescription);

        return $deviceModelAction->execute();
    }

    /**
     * Validate that the ram of the device on the invoice is the same as the one in the register.
     */
    public function validateRam(): DeviceAttributeValidationLog
    {
        $deviceRamAction = new DeviceRamValidationAction($this->device, $this->productDescription);

        return $deviceRamAction->execute();
    }

    /**
     * Validate that the storage of the device on the invoice is the same as the one in the register.
     */
    public function validateStorage(): DeviceAttributeValidationLog
    {
        $deviceStorageAction = new DeviceStorageValidationAction($this->device, $this->productDescription);

        return $deviceStorageAction->execute();
    }

    /**
     * Validate that the color of the device on the invoice is the same as the one on the register.
     */
    public function validateColor(): DeviceAttributeValidationLog
    {
        $deviceColorAction = new DeviceColorValidationAction($this->device, $this->productDescription);

        return $deviceColorAction->execute();
    }

    /**
     * validate that the IMEI 1 of the device on the invoice is the same as the one on the register.
     */
    public function validateImei1(): DeviceAttributeValidationLog
    {
        $deviceImei1Action = new DeviceImei1ValidationAction($this->device, $this->productDescription);

        return $deviceImei1Action->execute();
    }

    /**
     * Validate that the IMEI 2 of the device on the invoice is the same as the one on the record
     */
    public function validateImei2(): DeviceAttributeValidationLog
    {
        $deviceImei2Action = new DeviceImei2ValidationAction($this->device, $this->productDescription);

        return $deviceImei2Action->execute();
    }
}
