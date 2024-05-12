<?php

namespace App\Actions\DeviceInvoiceProductValidation;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Traits\StringNormalizer;
use Exception;
use FuzzyWuzzy\Fuzz;

class DeviceColorValidationAction
{
    use StringNormalizer;

    private Fuzz $fuzz;
    private string $deviceColor;
    private DeviceAttributeValidationLog $result;
    private const MIN_COLOR_SIMILARITY =
        DeviceAttributeValidationRatio::MIN_COLOR_SIMILARITY;

    public function __construct(
        private Device $device,
        private string $invoiceProduct,
    ) {
        $this->fuzz = new Fuzz();

        $this->deviceColor = $this->normalizeAttribute(
            $this->device->color
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
     * Normalizes the attribute by removing accents, extra white spaces,
     * and non alphanumeric values.
     */
    private function normalizeAttribute(string $attribute): string
    {
        return $this->removeNonAlphanumeric(
            $this->basicNormalize($attribute)
        );
    }

    /**
     * Calculate token set ratio score between device color and invoice product description.
     */
    private function calculateSimilarityRatio(): int
    {
        return $this->fuzz->tokenSetRatio(
            $this->deviceColor,
            $this->invoiceProduct
        );
    }

    /**
     * Persists the validation results in the database.
     */
    private function persistValidationResult(int $similarityRatio): void
    {
        $validated = $similarityRatio >= self::MIN_COLOR_SIMILARITY;

        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => Device::class,
            'attribute_label' => 'color',
            'attribute_value' => $this->deviceColor,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => self::MIN_COLOR_SIMILARITY,
            'validated' => $validated,
        ]);
    }
}
