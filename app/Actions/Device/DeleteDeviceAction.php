<?php

namespace App\Actions\Device;

use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DeleteDeviceAction
{
    public function __construct(private Device $device) {}

    public function execute(): bool
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->device->delete();

                return true;
            });
        } catch (Exception $e) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_delete.unable_to_delete'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function validateAttributesBeforeAction(): void
    {
        if ($this->device->validation_status !== DeviceValidationStatus::REJECTED) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_delete.invalid'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
