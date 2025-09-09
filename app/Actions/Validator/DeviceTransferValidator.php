<?php

namespace App\Actions\Validator;

use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\DeviceTransfer;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class DeviceTransferValidator
{
    public function __construct(
        private readonly ?DeviceTransfer $transfer
    ) {}

    /**
     * Creates a new instance for the validator.
     */
    public static function create(): self
    {
        return new self(null);
    }

    /**
     * Create a new instance for the validator with the given device transfer.
     */
    public static function for(DeviceTransfer $transfer): self
    {
        return new self($transfer);
    }

    /**
     * Validate that the target user is not the same as the source user.
     */
    public function mustNotTransferToSelf(User $sourceUser, User $targetUser): self
    {
        $isSameUser = $sourceUser->id === $targetUser->id;

        throw_if($isSameUser, new HttpJsonResponseException(
            trans('validators.device.transfer.same_user'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validate that there is no pending transfer for the device.
     */
    public function mustNotExistPendingTransfer(Device $device): self
    {
        $transfer = $device->lastTransfer();

        throw_if($transfer?->status->isPending(), new HttpJsonResponseException(
            trans('validators.device.transfer.pending'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validate that the given user is the source user of the transfer.
     */
    public function mustBeSourceUser(User $user): self
    {
        $isSourceUser = $user->id === $this->transfer?->source_user_id;

        throw_unless($isSourceUser, new HttpJsonResponseException(
            trans('validators.device.transfer.not_source_user'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validate that the given user is the target user of the transfer.
     */
    public function mustBeTargetUser(User $user): self
    {
        $isTargetUser = $user->id === $this->transfer?->target_user_id;

        throw_unless($isTargetUser, new HttpJsonResponseException(
            trans('validators.device.transfer.not_target_user'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validate that the device transfer status is 'pending'.
     */
    public function mustBePending(): self
    {
        $isPending = $this->transfer?->status->isPending();

        throw_unless($isPending, new HttpJsonResponseException(
            trans('validators.device.transfer.not_pending'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }
}
