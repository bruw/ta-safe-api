<?php

namespace App\Actions\DeviceTransfer\Accept;

use App\Actions\Validator\DeviceTransferValidator;
use App\Enums\Device\DeviceTransferStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\DeviceTransfer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AcceptDeviceTransferAction
{
    public function __construct(
        private readonly User $user,
        private DeviceTransfer $transfer
    ) {}

    public function execute(): DeviceTransfer
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->updateDeviceTransfer();
                $this->updateDeviceOwner();
                $this->logSuccess();

                return $this->transfer;
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
        DeviceTransferValidator::for($this->transfer)
            ->mustBeTargetUser($this->user)
            ->mustBePending();
    }

    /**
     * Updates the device transfer to be accepted.
     */
    private function updateDeviceTransfer(): void
    {
        $this->transfer->update(['status' => DeviceTransferStatus::ACCEPTED]);
    }

    /**
     * Updates the device owner to be the target user.
     */
    private function updateDeviceOwner(): void
    {
        $this->transfer->device->update(['user_id' => $this->user->id]);
    }

    /**
     * Log a success message for the device transfer acceptance.
     */
    private function logSuccess(): void
    {
        Log::info("The user {$this->user->name} successfully accepted device transfer.", [
            'user_id' => $this->user->id,
            'transfer_id' => $this->transfer->id,
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
     * Logs an error message for the device transfer acceptance failure.
     */
    private function logError(Exception $e): void
    {
        Log::error("The user {$this->user->name} failed to accept device transfer.", [
            'user_id' => $this->user->id,
            'transfer_id' => $this->transfer->id,
            'context' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    /**
     * Throws an exception when the device transfer acceptance fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.device_transfer.errors.accept'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
