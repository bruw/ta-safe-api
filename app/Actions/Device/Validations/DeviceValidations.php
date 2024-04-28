<?php

namespace App\Actions\Device\Validations;

use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use Symfony\Component\HttpFoundation\Response;

trait DeviceValidations
{
    public function validationStatusMustBePending(): void
    {
        if ($this->device->validation_status !== DeviceValidationStatus::PENDING) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_invoice.update_closed'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
