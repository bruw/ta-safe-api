<?php

namespace App\Services\DeviceInvoice;

use App\Actions\DeviceInvoice\Validation\Device\Brand\DeviceBrandValidationAction;
use App\Actions\DeviceInvoice\Validation\Device\Color\DeviceColorValidationAction;
use App\Actions\DeviceInvoice\Validation\Device\Model\DeviceModelNameValidationAction;
use App\Actions\DeviceInvoice\Validation\Device\Ram\DeviceRamValidationAction;
use App\Actions\DeviceInvoice\Validation\Device\Storage\DeviceStorageValidationAction;
use App\Actions\DeviceInvoice\Validation\User\Cpf\DeviceOwnerCpfValidationAction;
use App\Actions\DeviceInvoice\Validation\User\Name\DeviceOwnerNameValidationAction;
use App\Dto\Invoice\Search\InvoiceProductMatchResultDto;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;

class DeviceInvoiceValidationService
{
    public function __construct(
        private readonly Device $device,
        private readonly InvoiceProductMatchResultDto $invoiceProduct
    ) {}

    /**
     * Validate the CPF of the device owner against the CPF of the invoice consumer.
     */
    public function validateOwnerCpf(): DeviceAttributeValidationLog
    {
        return (new DeviceOwnerCpfValidationAction($this->device))->execute();
    }

    /**
     * Validate the name of the device owner against the name of the invoice consumer.
     */
    public function validateOwnerName(): DeviceAttributeValidationLog
    {
        return (new DeviceOwnerNameValidationAction($this->device))->execute();
    }

    /**
     * Validate the brand of the device against the brand of the invoice product.
     */
    public function validateBrand(): DeviceAttributeValidationLog
    {
        return (new DeviceBrandValidationAction(
            $this->device,
            $this->invoiceProduct->product
        ))->execute();
    }

    /**
     * Validate the model of the device against the model of the invoice product.
     */
    public function validateModel(): DeviceAttributeValidationLog
    {
        return (new DeviceModelNameValidationAction(
            $this->device,
            $this->invoiceProduct->product
        ))->execute();
    }

    /**
     * Validate the RAM of the device against the RAM of the invoice product.
     */
    public function validateRam(): DeviceAttributeValidationLog
    {
        return (new DeviceRamValidationAction(
            $this->device,
            $this->invoiceProduct->product
        ))->execute();
    }

    /**
     * Validate the storage of the device against the storage of the invoice product.
     */
    public function validateStorage(): DeviceAttributeValidationLog
    {
        return (new DeviceStorageValidationAction(
            $this->device,
            $this->invoiceProduct->product
        ))->execute();
    }

    /**
     * Validate the color of the device against the color of the invoice product.
     */
    public function validateColor(): DeviceAttributeValidationLog
    {
        return (new DeviceColorValidationAction(
            $this->device,
            $this->invoiceProduct->product
        ))->execute();
    }
}
