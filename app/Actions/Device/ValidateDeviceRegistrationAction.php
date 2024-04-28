<?php

namespace App\Actions\Device;

use App\Actions\Device\Validations\DeviceValidations;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ValidateDeviceRegistrationAction
{
    use DeviceValidations;

    public function __construct(
        private Device $device,
        private string $cpf,
        private string $name,
        private string $products
    ) {}

    /**
     * Runs the resources to validate a record.
     */
    public function execute(): bool
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $this->updateDeviceInvoice();

                return true;
            });
        } catch (Exception $e) {
            throw new HttpJsonResponseException(
                trans('validation.custom.device_validation.unable_to_validate'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Adds cpf, name and description to the invoice registered by the user in the device registry.
     */
    private function updateDeviceInvoice(): void
    {
        $this->device->invoice->update([
            'consumer_cpf' => $this->cpf,
            'consumer_name' => $this->name,
            'product_description' => $this->products,
        ]);
    }

    /**
     * Validate attributes against business rules before the action occurs.
     */
    private function validateAttributesBeforeAction(): void
    {
        $this->validationStatusMustBePending();
    }
}
