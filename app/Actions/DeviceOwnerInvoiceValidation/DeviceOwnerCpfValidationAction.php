<?php

namespace App\Actions\DeviceOwnerInvoiceValidation;

use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Models\User;
use App\Traits\StringNormalizer;
use Exception;

class DeviceOwnerCpfValidationAction
{
    use StringNormalizer;

    private string $deviceOwnerCpf;
    private string $invoiceConsumerCpf;
    private DeviceAttributeValidationLog $result;

    public function __construct(
        private Device $device,
    ) {
        $this->deviceOwnerCpf = $this->normalizeCpf($this->device->user->cpf);
        $this->invoiceConsumerCpf = $this->normalizeCpf($this->device->invoice->consumer_cpf);
    }

    /**
     * Run the validation process.
     */
    public function execute(): DeviceAttributeValidationLog
    {
        try {
            $isValidCpf = $this->compareCpf();
            $this->persistValidationResult($isValidCpf);
        } catch (Exception $e) {
            $this->persistValidationResult(0);
        } finally {
            return $this->result;
        }
    }

    /**
     * Normalize cpf by removing non-digit content and extra whitespace.
     */
    private function normalizeCpf(string $cpf): string
    {
        return $this->extractOnlyDigits($cpf);
    }

    /**
     * Compare device user CPF with invoice consumer CPF.
     */
    private function compareCpf(): bool
    {
        return $this->deviceOwnerCpf == $this->invoiceConsumerCpf;
    }

    /**
     * Persists the validation results in the database.
     */
    private function persistValidationResult(bool $isValidCpf): void
    {
        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => User::class,
            'attribute_label' => 'cpf',
            'attribute_value' => $this->deviceOwnerCpf,
            'invoice_attribute_label' => 'consumer_cpf',
            'invoice_attribute_value' => $this->invoiceConsumerCpf,
            'similarity_ratio' => $isValidCpf ? 100 : 0,
            'min_similarity_ratio' => 100,
            'validated' => $isValidCpf,
        ]);
    }
}
