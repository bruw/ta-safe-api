<?php

namespace App\Actions\Device\Create;

use App\Dto\Device\CreateDeviceDto;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CreateDeviceAction
{
    public function __construct(
        private readonly User $user,
        private readonly CreateDeviceDto $data
    ) {}

    /**
     * Register a new device with the given data.
     */
    public function execute(): Device
    {
        try {
            return DB::transaction(function () {
                $device = $this->createDevice();
                $this->createInvoice($device);
                $this->logSuccess($device);

                return $device;
            });
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Creates a new device.
     */
    private function createDevice(): Device
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
     * Creates a new invoice with the given device.
     */
    private function createInvoice(Device $device): void
    {
        Invoice::create([
            'device_id' => $device->id,
            'access_key' => $this->data->accessKey,
        ]);
    }

    /**
     * Logs a success message for the device registration.
     */
    private function logSuccess(Device $device): void
    {
        Log::info("The user {$this->user->name} created a new device.", [
            'user_id' => $this->user->id,
            'device_id' => $device->id,
        ]);
    }

    /**
     * Handles an exception that occurred during device registration.
     */
    private function handleException(Exception $e): void
    {
        Log::error("The user {$this->user->name} attempted to create a device, but an error occurred.", [
            'user_id' => $this->user->id,
            'context' => [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
        ]);

        throw new HttpJsonResponseException(
            trans('device.errors.create'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
