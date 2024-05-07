<?php

namespace App\Actions\DeviceOwnerInvoiceValidation;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Traits\StringNormalizer;
use Exception;
use FuzzyWuzzy\Fuzz;

class CpfAttributeValidationAction
{
    use StringNormalizer;

    private Fuzz $fuzz;
    private string $deviceUserCpf;
    private string $invoiceConsumerCpf;
    private DeviceAttributeValidationLog $result;

    private const MIN_CPF_SIMILARITY =
        DeviceAttributeValidationRatio::MIN_CPF_SIMILARITY;

    public function __construct(
        private Device $device,
    ) {
        $this->fuzz = new Fuzz();

        $this->deviceUserCpf = $this->normalizeCpf(
            $this->device->user->cpf
        );

        $this->invoiceConsumerCpf = $this->normalizeCpf(
            $this->device->invoice->consumer_cpf
        );
    }

    /**
     * Run the validation process.
     */
    public function execute(): DeviceAttributeValidationLog
    {
        try {
            $similarityRatio = $this->calculateRatio();
            $this->persistResult($similarityRatio);
        } catch (Exception $e) {
            $this->persistResult(0);
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
     * Returns the ratio score between userCpf and consumerCpf.
     */
    private function calculateRatio(): int
    {
        return $this->fuzz->tokenSetRatio(
            $this->deviceUserCpf,
            $this->invoiceConsumerCpf,
        );
    }

    /**
     * Persists the results in the database.
     */
    private function persistResult($similarityRatio): void
    {
        $validated = $similarityRatio == self::MIN_CPF_SIMILARITY;

        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_context' => get_class($this->device->user),
            'attribute_name' => 'cpf',
            'attribute_value' => $this->deviceUserCpf,
            'provided_value' => $this->invoiceConsumerCpf,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => self::MIN_CPF_SIMILARITY,
            'validated' => $validated,
        ]);
    }
}
