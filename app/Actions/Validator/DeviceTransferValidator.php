<?php

namespace App\Actions\Validator;

use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class DeviceTransferValidator
{
    public function __construct(
        private readonly Device $device,
        private readonly User $sourceUser,
        private readonly User $targetUser
    ) {}

    /**
     * Create a new DeviceValidator instance for the specified device.
     */
    public static function for(Device $device, User $sourceUser, User $targetUser): self
    {
        return new self($device, $sourceUser, $targetUser);
    }

    /**
     * Validate that the target user is not the same as the source user.
     */
    public function mustNotTransferToSelf(): self
    {
        $isSameUser = $this->sourceUser->id === $this->targetUser->id;

        throw_if($isSameUser, new HttpJsonResponseException(
            trans('validators.device.transfer.same_user'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validate that there is no pending transfer for the device.
     */
    public function mustNotExistPendingTransfer(): self
    {
        $transfer = $this->device->lastTransfer();

        throw_if($transfer?->status->isPending(), new HttpJsonResponseException(
            trans('validators.device.transfer.pending'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }
}
