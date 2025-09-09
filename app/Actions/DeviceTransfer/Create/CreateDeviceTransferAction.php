<?php

namespace App\Actions\DeviceTransfer\Create;

use App\Actions\Validator\DeviceTransferValidator;
use App\Actions\Validator\DeviceValidator;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\DeviceTransfer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CreateDeviceTransferAction
{
    public function __construct(
        private readonly User $user,
        private readonly User $targetUser,
        private readonly Device $device
    ) {}

    public function execute(): DeviceTransfer
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $transfer = $this->createTransfer();
                $this->logSuccess($transfer);

                return $transfer;
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
            ->statusMustBeValidated();

        DeviceTransferValidator::create()
            ->mustNotTransferToSelf($this->user, $this->targetUser)
            ->mustNotExistPendingTransfer($this->device);
    }

    /**
     * Create a new device transfer.
     */
    private function createTransfer(): DeviceTransfer
    {
        return DeviceTransfer::create([
            'device_id' => $this->device->id,
            'source_user_id' => $this->user->id,
            'target_user_id' => $this->targetUser->id,
        ]);
    }

    /**
     * Log a success message for the device transfer creation.
     */
    private function logSuccess(DeviceTransfer $transfer): void
    {
        Log::info("The user {$this->user->name} successfully created device transfer.", [
            'user_id' => $this->user->id,
            'target_user_id' => $this->targetUser->id,
            'transfer_id' => $transfer->id,
        ]);
    }

    /**
     * Handles an exception that occurred during the action execution.
     */
    private function handleException(Exception $e): never
    {
        $this->logError($e);
        $this->throwException();
    }

    /**
     * Log an error message for the device transfer creation failure.
     */
    private function logError(Exception $e): void
    {
        Log::error("The user {$this->user->name} failed to create device transfer.", [
            'user_id' => $this->user->id,
            'target_user_id' => $this->targetUser->id,
            'context' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    /**
     * Throws an exception when the device transfer creation fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.device_transfer.errors.create'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
