<?php

namespace App\Actions\Device;

use App\Enums\Device\DeviceTransferStatus;
use App\Exceptions\GeneralJsonException;

use App\Models\Device;
use App\Models\DeviceTransfer;
use App\Models\User;

use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AcceptDeviceTransferAction
{
    private readonly User $currentUser;
    private DeviceTransfer $deviceTransfer;
    private Device $device;

    public function __construct(User $currentUser, DeviceTransfer $deviceTransfer)
    {
        $this->currentUser = $currentUser;
        $this->deviceTransfer = $deviceTransfer;
        $this->device = $deviceTransfer->device;
    }

    public function execute(): bool
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->deviceTransfer->update([
                    'status' => DeviceTransferStatus::ACCEPTED
                ]);

                $this->device->update([
                    'user_id' => $this->currentUser->id
                ]);

                return true;
            });
        } catch (Exception $e) {
            throw new GeneralJsonException(
                trans('validation.custom.device_transfer.unable_to_accept_transfer'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function validateAttributesBeforeAction(): void
    {
        if ($this->deviceTransfer->status !== DeviceTransferStatus::PENDING) {
            throw new GeneralJsonException(
                trans('validation.custom.device_transfer.transfer_closed'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
