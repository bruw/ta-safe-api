<?php

namespace App\Actions\DeviceInvoiceProductValidation;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Traits\StringNormalizer;
use Exception;
use FuzzyWuzzy\Fuzz;

class DeviceImei1ValidationAction
{
    use StringNormalizer;

    private Fuzz $fuzz;
    private string $deviceImei1;
    private DeviceAttributeValidationLog $result;
    private const MIN_IMEI_SIMILARITY =
        DeviceAttributeValidationRatio::MIN_IMEI_SIMILARITY;

    public function __construct(
        private Device $device,
        private string $invoiceProduct,
    ) {
        $this->fuzz = new Fuzz();

        $this->deviceImei1 = $this->normalizeAttribute(
            $this->device->imei_1
        );

        $this->invoiceProduct = $this->normalizeAttribute(
            $invoiceProduct
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
            $this->persistValidationResult(0);
        } finally {
            return $this->result;
        }
    }

    /**
     * Normalizes the attribute by removing letters and special characters.
     */
    private function normalizeAttribute(string $attribute): string
    {
        return $this->extractOnlyDigits($attribute);
    }

    /**
     * Calculate ratio score between device imei_1 and invoice product description.
     */
    private function calculateSimilarityRatio(): int
    {
        return $this->fuzz->tokenSetRatio(
            $this->deviceImei1,
            $this->invoiceProduct
        );
    }

    /**
     * Persists the validation results in the database.
     */
    private function persistValidationResult(int $similarityRatio): void
    {
        $validated = $similarityRatio >= self::MIN_IMEI_SIMILARITY;

        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => Device::class,
            'attribute_label' => 'imei_1',
            'attribute_value' => $this->deviceImei1,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => self::MIN_IMEI_SIMILARITY,
            'validated' => $validated,
        ]);
    }
}
