<?php

namespace App\Actions\ValidateDeviceRegistration;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Traits\StringNormalizer;
use Exception;
use FuzzyWuzzy\Fuzz;

class DeviceOwnerInvoiceValidationAction
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
            $similarityRatio = $this->calculateRatio();
            $this->persistResult($similarityRatio);
        } catch (Exception $e) {
            $this->persistResult(0);
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
     * Calculates the ratio of similarity between names.
     */
    private function calculateRatio(): int
    {
        return $this->fuzz->ratio(
            $this->deviceOwnerName,
            $this->invoiceConsumerName
        );
    }

    /**
     * Persists the results in the database.
     */
    private function persistResult($similarityRatio): void
    {
        $validated = $similarityRatio >= self::MIN_NAME_SIMILARITY;

        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_context' => get_class($this->device->user),
            'attribute_name' => 'name',
            'attribute_value' => $this->deviceOwnerName,
            'provided_value' => $this->invoiceConsumerName,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => self::MIN_NAME_SIMILARITY,
            'validated' => $validated,
        ]);
    }
}
