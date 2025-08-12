<?php

namespace App\Actions\Device\Register;

use App\Dto\Device\RegisterDeviceDto;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RegisterDeviceAction
{
    public function __construct(
        private readonly User $user,
        private RegisterDeviceDto $data
    ) {}

    /**
     * Registers a device for the current user and logs its registration.
     */
    public function execute(): Device
    {
        try {
            return DB::transaction(function () {
                $device = $this->registerDevice();
                $this->registerInvoice($device);
                $this->logSuccess($device);

                return $device;
            });
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Registers a new device with the provided user and device data.
     */
    private function registerDevice(): Device
    {
        return Device::create([
            'user_id' => $this->user->id,
            'device_model_id' => $this->data->deviceModelId,
            'color' => $this->data->color,
            'imei_1' => $this->data->imei1,
            'imei_2' => $this->data->imei2,
        ]);
    }

    /**
     * Creates an invoice for a device.
     */
    private function registerInvoice(Device $device): void
    {
        Invoice::create([
            'device_id' => $device->id,
            'access_key' => $this->data->accessKey,
        ]);
    }

    /**
     * Logs an info message when a device registration attempt succeeds.
     */
    private function logSuccess(Device $device): void
    {
        Log::info("The user {$this->user->name} successfully registered device.", [
            'user_id' => $this->user->id,
            'device_id' => $device->id,
        ]);
    }

    /**
     * Handles an exception that occurred during the registration of a device.
     */
    private function handleException(Exception $e): never
    {
        $this->logError($e);
        $this->throwException();
    }

    /**
     * Logs an error message when a device registration attempt fails.
     */
    private function logError(Exception $e): void
    {
        Log::error("The user {$this->user->name} failed to register device.", [
            'user_id' => $this->user->id,
            'context' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    /**
     * Throws an exception when a registration attempt fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.device.errors.register'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
