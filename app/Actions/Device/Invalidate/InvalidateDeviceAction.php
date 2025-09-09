<?php

namespace App\Actions\Device\Invalidate;

use App\Actions\Validator\DeviceValidator;
use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InvalidateDeviceAction
{
    public function __construct(
        private readonly User $user,
        private readonly Device $device,
    ) {}

    public function execute(): Device
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->updateDeviceStatus();
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
     * Updates the device status to 'rejected'.
     */
    private function updateDeviceStatus(): void
    {
        $this->device->update(['validation_status' => DeviceValidationStatus::REJECTED]);
    }

    /**
     * Logs a success message for the device invalidation.
     */
    private function logSuccess(): void
    {
        Log::info("The user {$this->user->name} successfully invalidated device.", [
            'user_id' => $this->user->id,
            'device_id' => $this->device->id,
        ]);
    }

    /**
     * Handles an exception that occurred during the device invalidation.
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
        Log::error("The user {$this->user->name} failed to invalidate the device.", [
            'user_id' => $this->user->id,
            'device_id' => $this->device->id,
            'context' => [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
        ]);
    }

    /**
     * Throws an exception when a device invalidation attempt fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.device.errors.invalidate'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
