<?php

namespace App\Actions\Device;

use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\User;

use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RegisterDeviceAction
{
    private readonly User $currentUser;
    private readonly array $data;

    public function __construct(User $currentUser, array $data)
    {
        $this->currentUser = $currentUser;
        $this->data = $data;
    }

    public function execute(): bool
    {
        try {
            return DB::transaction(function () {
                $device = Device::create([
                    'user_id' => $this->currentUser->id,
                    'device_model_id' => $this->data['device_model_id'],
                    'color' => mb_convert_case($this->data['color'], MB_CASE_TITLE),
                    'imei_1' => $this->data['imei_1'],
                    'imei_2' => $this->data['imei_2']
                ]);

                Invoice::create([
                    'access_key' => $this->data['access_key'],
                    'device_id' => $device->id
                ]);

                return true;
            });
        } catch (Exception $e) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_registration.unable_to_register_device'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
