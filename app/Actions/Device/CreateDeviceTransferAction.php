<?php

namespace App\Actions\Device;

use App\Enums\Device\DeviceTransferStatus;
use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\DeviceTransfer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CreateDeviceTransferAction
{
    public function __construct(
        private User $currentUser,
        private User $targetUser,
        private Device $device) {}

    public function execute(): bool
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                DeviceTransfer::create([
                    'device_id' => $this->device->id,
                    'source_user_id' => $this->currentUser->id,
                    'target_user_id' => $this->targetUser->id,
                ]);

                return true;
            });
        } catch (Exception $e) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_transfer.unable_to_create_transfer'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function validateAttributesBeforeAction(): void
    {
        $lastDeviceTransfer = $this->device->lastTransfer();

        if ($this->currentUser->id != $this->device->user->id) {
            throw new HttpJsonResponseException(
                trans('auth.unauthorized'),
                Response::HTTP_FORBIDDEN
            );
        }

        if ($this->currentUser->id == $this->targetUser->id) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_transfer.not_yourself'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($lastDeviceTransfer) {
            if ($lastDeviceTransfer->status == DeviceTransferStatus::PENDING) {
                throw new HttpJsonResponseException(
                    trans('validation.custom.device_transfer.in_progress'),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        }

        if ($this->device->validation_status !== DeviceValidationStatus::VALIDATED) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_transfer.register_not_validated'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
