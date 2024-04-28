<?php

namespace App\Actions\Device;

use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DeviceRegistrationValidationAction
{
    /**
     * TODO DESCRIPTION
     */
    public function __construct(
        private Device $device,
        private string $cpf,
        private string $name,
        private string $products
    ) {
    }

    /**
     * TODO DESCRIPTION
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
                'Não foi possível atualizar os dados da nota fiscal deste aparelho',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * TODO DESCRIPTION
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
     * TODO DESCRIPTION
     */
    private function validateAttributesBeforeAction(): void
    {
        if ($this->device->validation_status !== DeviceValidationStatus::PENDING) {
            throw new HttpJsonResponseException(
                'Não é possível atualiza a nota fiscal deste aparelho',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
