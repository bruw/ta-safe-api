<?php

namespace App\Actions\Validator;

use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class DeviceValidator
{
    public function __construct(
        private readonly Device $device
    ) {}

    /**
     * Creates a new instance of the validator for the given device.
     */
    public static function for(Device $device): self
    {
        return new self($device);
    }

    /**
     * Asserts that the given user is the owner of the device.
     */
    public function userMustBeOwner(User $user): self
    {
        throw_if($user->id !== $this->device->user_id, new HttpJsonResponseException(
            trans('device_validator.user_must_be_owner'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validate if the device status is 'rejected'.
     */
    public function validationStatusMustBeRejected(): self
    {
        $isRejected = $this->device->validation_status->isRejected();

        throw_unless($isRejected, new HttpJsonResponseException(
            trans('device_validator.status_must_be_rejected'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }
}
