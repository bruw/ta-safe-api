<?php

namespace App\Jobs\Device;

use App\Enums\Device\DeviceValidationStatus;
use App\Models\Device;
use App\Services\DeviceRegisterValidations\DeviceInvoiceValidationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ValidateDeviceRegistrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private DeviceInvoiceValidationService $validationService;

    /**
     * Create a new job instance.
     */
    public function __construct(private Device $device)
    {
        $this->validationService = new DeviceInvoiceValidationService($device);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $criticalLogs = $this->executeCriticalValidations();
        $this->executeNonCriticalValidations();

        $this->validateByLogs($criticalLogs);
    }

    /**
     * Execute the actions that determine whether a record is valid;
     * All must be approved to make a record valid.
     */
    private function executeCriticalValidations(): array
    {
        return [
            'ownerCpfLog' => $this->validationService->validateOwnerCpf(),
            'ownerNameLog' => $this->validationService->validateOwnerName(),
            'brandLog' => $this->validationService->validateBrand(),
            'modelNameLog' => $this->validationService->validateModel(),
            'ramLog' => $this->validationService->validateRam(),
            'storageLog' => $this->validationService->validateStorage(),
        ];
    }

    /**
     * Action important for some product features;
     */
    private function executeNonCriticalValidations(): void
    {
        $this->validationService->validateColor();
        $this->validationService->validateImei1();
        $this->validationService->validateImei2();
    }

    /**
     * Checks the critical validation logs to determine whether the record is valid or not.
     */
    private function validateByLogs(array $logs): void
    {
        $isValid = $logs['ownerCpfLog']->validated
            && $logs['ownerNameLog']->validated
            && $logs['brandLog']->validated
            && $logs['modelNameLog']->validated
            && ($logs['ramLog']->validated || $logs['storageLog']->validated);

        $this->updateDeviceRegistrationStatus($isValid);
    }

    /**
     * Updates device validation status;
     * Can be validated or rejecteded depending on critical validations.
     */
    private function updateDeviceRegistrationStatus(bool $isValid): void
    {
        $status = $isValid
            ? DeviceValidationStatus::VALIDATED
            : DeviceValidationStatus::REJECTED;

        $this->device->update([
            'validation_status' => $status,
        ]);
    }
}
