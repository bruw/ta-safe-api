<?php

namespace App\Actions\Device;

use App\Exceptions\GeneralJsonException;

use App\Models\Device;
use App\Models\Invoice;
use App\Models\User;

use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RegisterDeviceAction
{
    private readonly User $currentUser;
    private readonly string $deviceModelId;
    private readonly string $color;
    private readonly string $accessKey;

    public function __construct(
        User $currentUser,
        string $deviceModelId,
        string $color,
        string $accessKey
    ) {
        $this->currentUser = $currentUser;
        $this->deviceModelId = $deviceModelId;
        $this->color = mb_convert_case($color, MB_CASE_TITLE);
        $this->accessKey = $accessKey;
    }

    public function execute(): bool
    {
        try {
            return DB::transaction(function () {
                $device = Device::create([
                    'user_id' => $this->currentUser->id,
                    'device_model_id' => $this->deviceModelId,
                    'color' => $this->color
                ]);

                Invoice::create([
                    'access_key' => $this->accessKey,
                    'device_id' => $device->id
                ]);

                return true;
            });
        } catch (Exception $e) {
            throw new GeneralJsonException(
                trans('validation.custom.device_registration.unable_to_register_device'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
