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
    public function __construct(
        private readonly User $user,
        private Device $device
    ) {}

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
     * Validate attributes against business rules before the action occurs.
     */
    private function validateAttributesBeforeAction(): void
    {
        DeviceValidator::for($this->device)
            ->mustBeOwner($this->user)
            ->statusMustBeRejected();
    }

    /**
     * Logs an info message when a device deletion attempt succeeds.
     */
    private function logSuccess(): void
    {
        Log::info("The user {$this->user->name} successfully deleted device.", [
            'user_id' => $this->user->id,
            'context' => [
                'model' => $this->device->deviceModel->name,
                'color' => $this->device->color,
                'imei_1' => $this->device->imei_1,
                'imei_2' => $this->device->imei_2,
            ],
        ]);
    }

    /**
     * Handles an exception that occurred during the deletion of a device.
     */
    private function handleException(Exception $e): never
    {
        $this->logError($e);
        $this->throwException();
    }

    /**
     * Logs an error message when a device deletion attempt fails.
     */
    private function logError(Exception $e): void
    {
        Log::error("The user {$this->user->name} failed to delete device.", [
            'user_id' => $this->user->id,
            'device_id' => $this->device->id,
            'context' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    /**
     * Throws an exception when a device deletion attempt fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.device.errors.delete'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
