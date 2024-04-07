<?php

namespace App\Actions\Device;

use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\DeviceSharingToken;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CreateSharingTokenAction
{
    public function __construct(private Device $device) {}

    public function execute(): DeviceSharingToken
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $sharingToken = $this->device->sharingToken()->firstOrNew();

                if (! $sharingToken->exists) {
                    $sharingToken->token = $this->generateToken();
                    $sharingToken->expires_at = now()->addHours(24);
                    $sharingToken->save();
                } else {
                    $sharingToken->update([
                        'token' => $this->generateToken(),
                        'expires_at' => now()->addHours(24),
                    ]);
                }

                return $sharingToken;
            });
        } catch (Exception $e) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_sharing_token.unable_to_create_token'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function validateAttributesBeforeAction(): void
    {
        if ($this->device->validation_status !== DeviceValidationStatus::VALIDATED) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_sharing_token.register_not_validated'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    private function generateToken(): string
    {
        do {
            $randomNumber = mt_rand(1, 99999999);
            $token = str_pad($randomNumber, 8, '0', STR_PAD_LEFT);

            $tokenExists = DeviceSharingToken::where('token', $token)->exists();

            if (! $tokenExists) {
                return $token;
            }
        } while ($tokenExists);
    }
}
