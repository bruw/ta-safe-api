<?php

namespace App\Actions\Device;

use App\Enums\Device\DeviceTransferStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\DeviceTransfer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AcceptDeviceTransferAction
{
    private Device $device;

    public function __construct(
        private User $targetUser,
        private DeviceTransfer $deviceTransfer
    ) {
        $this->device = $deviceTransfer->device;
    }

    public function execute(): bool
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->deviceTransfer->update([
                    'status' => DeviceTransferStatus::ACCEPTED,
                ]);

                $this->device->update([
                    'user_id' => $this->targetUser->id,
                ]);

                return true;
            });
        } catch (Exception $e) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_transfer.unable_to_accept_transfer'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function validateAttributesBeforeAction(): void
    {
        if ($this->deviceTransfer->status !== DeviceTransferStatus::PENDING) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_transfer.transfer_closed'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
