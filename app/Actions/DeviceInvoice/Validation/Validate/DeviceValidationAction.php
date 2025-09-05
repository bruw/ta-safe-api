<?php

namespace App\Actions\DeviceInvoice\Validation\Validate;

use App\Actions\DeviceInvoice\ProductMatch\InvoiceProductMatchAction;
use App\Actions\Validator\DeviceValidator;
use App\Dto\Invoice\Search\InvoiceProductMatchResultDto;
use App\Enums\Device\DeviceValidationStatus;
use App\Models\Device;
use App\Services\DeviceInvoice\DeviceInvoiceValidationService;
use Exception;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceValidationAction
{
    private InvoiceProductMatchResultDto $invoiceProduct;
    private DeviceInvoiceValidationService $deviceValidationService;
    private SupportCollection $mandatoryValidationLogs;

    public function __construct(private readonly Device $device) {}

    public function execute(): Device
    {
        try {
            $this->initialize();

            return DB::transaction(function () {
                $this->validateAttributesBeforeAction();

                $this->mandatoryValidations();
                $this->optionalValidations();
                $this->validateByLogs();
                $this->logSuccess();

                return $this->device;
            });
        } catch (Exception $e) {
            $this->logError($e);

            return $this->device;
        }
    }

    /**
     * Initialize attributes for the validation process.
     */
    private function initialize(): void
    {
        $this->invoiceProduct = (new InvoiceProductMatchAction(
            device: $this->device
        ))->execute();

        $this->deviceValidationService = new DeviceInvoiceValidationService(
            device: $this->device,
            invoiceProduct: $this->invoiceProduct
        );
    }

    private function validateAttributesBeforeAction(): void
    {
        DeviceValidator::for($this->device)
            ->statusMustBeInAnalysis();
    }

    /**
     * Performs the mandatory validations for the device validation process.
     */
    private function mandatoryValidations(): void
    {
        $this->mandatoryValidationLogs = collect([
            'ownerCpfLog' => $this->deviceValidationService->validateOwnerCpf(),
            'ownerNameLog' => $this->deviceValidationService->validateOwnerName(),
            'brandLog' => $this->deviceValidationService->validateBrand(),
            'modelNameLog' => $this->deviceValidationService->validateModel(),
            'ramLog' => $this->deviceValidationService->validateRam(),
            'storageLog' => $this->deviceValidationService->validateStorage(),
        ]);
    }

    /**
     * Performs the optional validations for the device validation process.
     */
    private function optionalValidations(): void
    {
        $this->deviceValidationService->validateColor();
    }

    /**
     * Checks if the mandatory validation logs are valid, and if either the RAM or
     * storage validation logs are valid.
     */
    private function validateByLogs(): void
    {
        $isValid = $this->mandatoryValidationLogs->only([
            'ownerCpfLog',
            'ownerNameLog',
            'brandLog',
            'modelNameLog',
        ])->every(fn ($log) => $log->validated) && (
            $this->mandatoryValidationLogs['ramLog']->validated ||
            $this->mandatoryValidationLogs['storageLog']->validated
        );

        $this->updateDeviceRegistrationStatus($isValid);
    }

    /**
     * Updates the device registration status.
     */
    private function updateDeviceRegistrationStatus(bool $isValid): void
    {
        $status = $isValid
            ? DeviceValidationStatus::VALIDATED
            : DeviceValidationStatus::REJECTED;

        $this->device->update(['validation_status' => $status]);
    }

    /**
     * Logs a success message for the device validation process.
     */
    private function logSuccess(): void
    {
        Log::info('Device validation success.', [
            'device_id' => $this->device->id,
        ]);
    }

    /**
     * Logs an error message for the device validation failure.
     */
    private function logError(Exception $e): void
    {
        Log::error('Device validation failure.', [
            'device_id' => $this->device->id,
            'context' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }
}
