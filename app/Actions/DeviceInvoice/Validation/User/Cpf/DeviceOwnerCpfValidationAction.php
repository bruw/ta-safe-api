<?php

namespace App\Actions\DeviceInvoice\Validation\User\Cpf;

use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Models\User;
use App\Utils\StringNormalize;

class DeviceOwnerCpfValidationAction
{
    private string $deviceOwnerCpf;
    private string $invoiceConsumerCpf;

    public function __construct(
        private readonly Device $device,
    ) {
        $this->deviceOwnerCpf = $this->normalize($this->device->user->cpf);
        $this->invoiceConsumerCpf = $this->normalize($this->device->invoice->consumer_cpf);
    }

    /**
     * Runs the validation process.
     */
    public function execute(): DeviceAttributeValidationLog
    {
        return $this->persistValidationResult(isValidCpf: $this->cpfMatch());
    }

    /**
     * Returns the given value normalized.
     */
    private function normalize(string $value): string
    {
        return StringNormalize::for($value)
            ->keepOnlyDigits()
            ->removeExtraWhiteSpaces()
            ->get();
    }

    /**
     * Check if the device owner CPF matches the invoice consumer CPF.
     */
    private function cpfMatch(): bool
    {
        return $this->deviceOwnerCpf == $this->invoiceConsumerCpf;
    }

    /**
     * Persist the validation result of the owner CPF validation.
     */
    private function persistValidationResult(bool $isValidCpf): DeviceAttributeValidationLog
    {
        return DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => User::class,
            'attribute_label' => 'cpf',
            'attribute_value' => $this->device->user->cpf,
            'invoice_attribute_label' => 'consumer_cpf',
            'invoice_attribute_value' => $this->device->invoice->consumer_cpf,
            'similarity_ratio' => $isValidCpf ? 100 : 0,
            'min_similarity_ratio' => 100,
            'validated' => $isValidCpf,
        ]);
    }
}
