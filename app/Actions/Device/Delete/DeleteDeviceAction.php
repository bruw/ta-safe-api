<?php

namespace App\Actions\Device\Delete;

use App\Actions\Validator\DeviceValidator;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DeleteDeviceAction
{
    private readonly array $deviceSnapshot;

    public function __construct(
        private readonly User $user,
        private Device $device
    ) {
        $this->deviceSnapshot = $device->getAttributes();
    }

    /**
     * Deletes the device.
     */
    public function execute(): bool
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->device->delete();
                $this->logSuccess();

                return true;
            });
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Validates attributes against business rules before the action occurs.
     */
    private function validateAttributesBeforeAction(): void
    {
        DeviceValidator::for($this->device)
            ->userMustBeOwner($this->user)
            ->validationStatusMustBeRejected();
    }

    /**
     * Logs a success message when a device is successfully deleted.
     */
    private function logSuccess(): void
    {
        Log::info("The user {$this->user->name} deleted a device.", [
            'user_id' => $this->user->id,
            'device_snapshot' => $this->deviceSnapshot,
        ]);
    }

    /**
     * Logs an error and throws an HttpJsonResponseException
     * when an exception occurs during device deletion.
     */
    private function handleException(Exception $e): void
    {
        Log::error("The user {$this->user->name} attempted to delete a device, but an error occurred.", [
            'user_id' => $this->user->id,
            'device_id' => $this->device->id,
            'context' => [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
        ]);

        throw new HttpJsonResponseException(
            trans('actions.device.errors.delete'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
