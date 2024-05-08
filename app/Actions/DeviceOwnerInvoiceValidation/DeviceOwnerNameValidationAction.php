<?php

namespace App\Actions\DeviceOwnerInvoiceValidation;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Traits\StringNormalizer;
use Exception;
use FuzzyWuzzy\Fuzz;

class DeviceOwnerNameValidationAction
{
    use StringNormalizer;

    private Fuzz $fuzz;
    private string $deviceOwnerName;
    private string $invoiceConsumerName;
    private DeviceAttributeValidationLog $result;
    private const MIN_NAME_SIMILARITY = DeviceAttributeValidationRatio::MIN_NAME_SIMILARITY;

    public function __construct(
        private Device $device,
    ) {
        $this->fuzz = new Fuzz();

        $this->deviceOwnerName = $this->normalizeName(
            $this->device->user->name
        );

        $this->invoiceConsumerName = $this->normalizeName(
            $this->device->invoice->consumer_name
        );
    }

    /**
     * Run the validation process.
     */
    public function execute(): DeviceAttributeValidationLog
    {
        try {
            $similarityRatio = $this->calculateSimilarityRatio();
            $this->persistValidationResult($similarityRatio);
        } catch (Exception $e) {
            dd($e);
            $this->persistValidationResult(0);
        } finally {
            return $this->result;
        }
    }

    /**
     * Normalizes the attribute name by removing accents, digits and special characters.
     */
    private function normalizeName(string $name): string
    {
        return $this->extractOnlyLetters($name);
    }

    /**
     * Calculate ratio score between deviceOwnerName and invoiceConsumerName.
     */
    private function calculateSimilarityRatio(): int
    {
        return $this->fuzz->ratio(
            $this->deviceOwnerName,
            $this->invoiceConsumerName
        );
    }

    /**
     * Persists the validation results in the database.
     */
    private function persistValidationResult($similarityRatio): void
    {
        $validated = $similarityRatio >= self::MIN_NAME_SIMILARITY;

        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => get_class($this->device->user),
            'attribute_label' => 'name',
            'attribute_value' => $this->deviceOwnerName,
            'invoice_attribute_label' => 'consumer_name',
            'invoice_attribute_value' => $this->invoiceConsumerName,
            'invoice_validated_value' => $validated ? $this->invoiceConsumerName : null,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => self::MIN_NAME_SIMILARITY,
            'validated' => $validated,
        ]);
    }
}
