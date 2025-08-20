<?php

namespace App\Actions\Device\Validate;

use App\Actions\Validator\DeviceValidator;
use App\Dto\Device\Invoice\DeviceInvoiceDto;
use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StartDeviceValidationAction
{
    public function __construct(
        private readonly User $user,
        private readonly Device $device,
        private readonly DeviceInvoiceDto $data,
    ) {}

    public function execute(): Device
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->updateDeviceStatus();
                $this->updateDeviceInvoice();
                $this->logSuccess();

                return $this->device;
            });
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Validates the device attributes before the action occurs.
     */
    private function validateAttributesBeforeAction(): void
    {
        DeviceValidator::for($this->device)
            ->mustBeOwner($this->user)
            ->statusMustBePending();
    }

    /**
     * Updates the device status to 'in analysis'.
     */
    private function updateDeviceStatus(): void
    {
        $this->device->update(['validation_status' => DeviceValidationStatus::IN_ANALYSIS]);
    }

    /**
     * Updates the invoice associated with the device.
     */
    private function updateDeviceInvoice(): void
    {
        $this->device->invoice->update([
            'consumer_cpf' => $this->data->cpf,
            'consumer_name' => $this->data->name,
            'product_description' => $this->data->products,
        ]);
    }

    /**
     * Logs a success message for the device validation start.
     */
    private function logSuccess(): void
    {
        Log::info("The user {$this->user->name} successfully started device validation.", [
            'user_id' => $this->user->id,
            'device_id' => $this->device->id,
        ]);
    }

    /**
     * Handles an exception that occurred during the device validation.
     */
    private function handleException(Exception $e): never
    {
        $this->logError($e);
        $this->throwException();
    }

    /**
     * Logs an error message for the device validation failure.
     */
    private function logError(Exception $e): void
    {
        Log::error("The user {$this->user->name} failed to start device validation.", [
            'user_id' => $this->user->id,
            'device_id' => $this->device->id,
            'context' => [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
        ]);
    }

    /**
     * Throws an exception when the device validation fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.device.errors.validate'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
