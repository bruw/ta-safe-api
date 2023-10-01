<?php

namespace App\Actions\Device;

use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\GeneralJsonException;

use App\Models\Device;
use App\Models\DeviceSharingToken;

use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CreateSharingTokenAction
{
    private readonly Device $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function execute(): bool
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                if (!$this->device->sharingToken) {
                    DeviceSharingToken::create([
                        'device_id' => $this->device->id,
                        'token' => $this->generateToken(),
                        'expires_at' => now()->addHours(24)
                    ]);

                    return true;
                }

                $this->device->sharingToken->update([
                    'token' => $this->generateToken(),
                    'expires_at' => now()->addHours(24)
                ]);

                return true;
            });
        } catch (Exception $e) {
            throw new GeneralJsonException(
                trans('validation.custom.device_sharing_token.unable_to_create_token'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function validateAttributesBeforeAction(): void
    {
        if ($this->device->validation_status !== DeviceValidationStatus::VALIDATED) {
            throw new GeneralJsonException(
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

            $tokenExists = DeviceSharingToken::where([
                'token' => $token
            ])->first();

            if (!$tokenExists) {
                return $token;
            }
        } while ($tokenExists);
    }
}
