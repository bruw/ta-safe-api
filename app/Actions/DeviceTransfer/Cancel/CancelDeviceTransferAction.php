<?php

namespace App\Actions\DeviceTransfer\Cancel;

use App\Actions\Validator\DeviceTransferValidator;
use App\Enums\Device\DeviceTransferStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\DeviceTransfer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CancelDeviceTransferAction
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
                $this->cancelTransfer();
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
            ->mustBeSourceUser($this->user)
            ->mustBePending();
    }

    /**
     * Update the device transfer status to 'canceled'.
     */
    private function cancelTransfer(): void
    {
        $this->transfer->update(['status' => DeviceTransferStatus::CANCELED]);
    }

    /**
     * Logs an info message when a device transfer cancellation attempt succeeds.
     */
    private function logSuccess(): void
    {
        Log::info("The user {$this->user->name} successfully canceled a device transfer.", [
            'user_id' => $this->user->id,
            'transfer_id' => $this->transfer->id,
        ]);
    }

    /**
     * Handles an exception that occurred during the device transfer cancellation.
     */
    private function handleException(Exception $e): never
    {
        $this->logError($e);
        $this->throwException();
    }

    /**
     * Logs an error message when a device transfer cancellation attempt fails.
     */
    private function logError(Exception $e): void
    {
        Log::error("The user {$this->user->name} failed to cancel a device transfer.", [
            'user_id' => $this->user->id,
            'transfer_id' => $this->transfer->id,
            'context' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    /**
     * Throws an exception when a device transfer cancellation attempt fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.device_transfer.errors.cancel'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
