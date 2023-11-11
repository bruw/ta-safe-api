<?php

namespace App\Actions\Device;

use App\Enums\Device\DeviceTransferStatus;
use App\Exceptions\GeneralJsonException;
use App\Models\DeviceTransfer;

use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RejectDeviceTransferAction
{
    private DeviceTransfer $deviceTransfer;

    public function __construct(DeviceTransfer $deviceTransfer)
    {
        $this->deviceTransfer = $deviceTransfer;
    }

    public function execute(): bool
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->deviceTransfer->update([
                    'status' => DeviceTransferStatus::REJECTED
                ]);

                return true;
            });
        } catch (Exception $e) {
            throw new GeneralJsonException(
                trans('validation.custom.device_transfer.unable_to_reject_transfer'),
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
