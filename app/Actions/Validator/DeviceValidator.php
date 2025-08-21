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
     * Create a new DeviceValidator instance for the specified device.
     */
    public static function for(Device $device): self
    {
        return new self($device);
    }

    /**
     * Validate if the given user is the owner of the device.
     */
    public function mustBeOwner(User $user): self
    {
        $isOwner = $user->id === $this->device->user_id;

        throw_unless($isOwner, new HttpJsonResponseException(
            trans('validators.device.user.owner'),
            Response::HTTP_FORBIDDEN
        ));

        return $this;
    }

    /**
     * Validate if the device status is 'rejected'.
     */
    public function statusMustBeRejected(): self
    {
        $isRejected = $this->device->validation_status->isRejected();

        throw_unless($isRejected, new HttpJsonResponseException(
            trans('validators.device.status.rejected'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validate if the device status is 'validated'.
     */
    public function statusMustBeValidated(): self
    {
        $isValidate = $this->device->validation_status->isValidated();

        throw_unless($isValidate, new HttpJsonResponseException(
            trans('validators.device.status.validated'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validate if the device status is 'pending'.
     */
    public function statusMustBePending(): self
    {
        $isPending = $this->device->validation_status->isPending();

        throw_unless($isPending, new HttpJsonResponseException(
            trans('validators.device.status.pending'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }
}
