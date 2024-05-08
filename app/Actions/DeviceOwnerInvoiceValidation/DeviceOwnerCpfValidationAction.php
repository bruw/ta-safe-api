<?php

namespace App\Actions\DeviceOwnerInvoiceValidation;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Traits\StringNormalizer;
use Exception;
use FuzzyWuzzy\Fuzz;
use FuzzyWuzzy\Process;

class DeviceOwnerCpfValidationAction
{
    use StringNormalizer;

    private Fuzz $fuzz;
    private Process $process;
    private string $deviceOwnerCpf;
    private string $invoiceConsumerCpf;
    private array $bestCpfMatch;
    private DeviceAttributeValidationLog $result;
    private const MIN_CPF_SIMILARITY = DeviceAttributeValidationRatio::MIN_CPF_SIMILARITY;

    public function __construct(
        private Device $device,
    ) {
        $this->fuzz = new Fuzz();
        $this->process = new Process();
        $this->deviceOwnerCpf = $this->normalizeCpf($this->device->user->cpf);
        $this->invoiceConsumerCpf = $this->normalizeCpf($this->device->invoice->consumer_cpf);
    }

    /**
     * Run the validation process.
     */
    public function execute(): DeviceAttributeValidationLog
    {
        try {
            $this->calculateSimilarityRatio();
            $this->persistValidationResult($this->bestCpfMatch[1]);
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
     * Calculate ratio score between deviceuserCpf and invoiceConsumerCpf.
     */
    private function calculateSimilarityRatio(): void
    {
        $consumerCpfArray = explode(' ', $this->invoiceConsumerCpf);

        $this->bestCpfMatch = $this->process->extract(
            $this->deviceOwnerCpf, $consumerCpfArray, null, [$this->fuzz, 'ratio']
        )[0];
    }

    /**
     * Persists the validation results in the database.
     */
    private function persistValidationResult($similarityRatio): void
    {
        $validated = $similarityRatio == self::MIN_CPF_SIMILARITY;

        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => get_class($this->device->user),
            'attribute_label' => 'cpf',
            'attribute_value' => $this->deviceOwnerCpf,
            'invoice_attribute_label' => 'consumer_cpf',
            'invoice_attribute_value' => $this->invoiceConsumerCpf,
            'invoice_validated_value' => $validated ? $this->bestCpfMatch[0] : null,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => self::MIN_CPF_SIMILARITY,
            'validated' => $validated,
        ]);
    }
}
