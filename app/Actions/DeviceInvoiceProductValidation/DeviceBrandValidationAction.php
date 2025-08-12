<?php

namespace App\Actions\DeviceInvoiceProductValidation;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Traits\StringNormalizer;
use Exception;
use FuzzyWuzzy\Fuzz;

class DeviceBrandValidationAction
{
    use StringNormalizer;

    private Fuzz $fuzz;
    private string $deviceBrand;
    private DeviceAttributeValidationLog $result;
    private const MIN_BRAND_SIMILARITY = DeviceAttributeValidationRatio::MIN_BRAND_SIMILARITY;

    public function __construct(
        private Device $device,
        private string $invoiceProduct,
    ) {
        $this->fuzz = new Fuzz;

        $this->deviceBrand = $this->normalizeAttribute(
            $this->device->deviceModel->brand->name
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
     * Normalizes the attribute by removing accents, digits, and special characters.
     */
    private function normalizeAttribute(string $attribute): string
    {
        return $this->extractOnlyLetters($attribute);
    }

    /**
     * Calculate ratio score between deviceBrand and invoiceProductDescriptions.
     */
    private function calculateSimilarityRatio(): int
    {
        return $this->fuzz->tokenSetRatio(
            $this->deviceBrand,
            $this->invoiceProduct
        );
    }

    /**
     * Persists the validation results in the database.
     */
    private function persistValidationResult(int $similarityRatio): void
    {
        $validated = $similarityRatio >= self::MIN_BRAND_SIMILARITY;

        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => Brand::class,
            'attribute_label' => 'brand_name',
            'attribute_value' => $this->deviceBrand,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => self::MIN_BRAND_SIMILARITY,
            'validated' => $validated,
        ]);
    }
}
