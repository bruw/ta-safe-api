<?php

namespace App\Actions\DeviceOwnerInvoiceValidation;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Traits\StringNormalizer;
use Exception;
use FuzzyWuzzy\Fuzz;
use FuzzyWuzzy\Process;

class DeviceBrandValidationAction
{
    use StringNormalizer;

    private Fuzz $fuzz;
    private Process $process;
    private string $deviceBrand;
    private string $invoiceProductDescriptions;
    private array $bestBrandMatch;
    private DeviceAttributeValidationLog $result;
    private const MIN_BRAND_SIMILARITY = DeviceAttributeValidationRatio::MIN_BRAND_SIMILARITY;

    public function __construct(
        private Device $device,
    ) {
        $this->fuzz = new Fuzz();
        $this->process = new Process();

        $this->deviceBrand = $this->normalizeBrand(
            $this->device->deviceModel->brand->name
        );

        $this->invoiceProductDescriptions = $this->normalizeInvoiceProductDescriptions(
            $this->device->invoice->product_description
        );
    }

    /**
     * Run the validation process.
     */
    public function execute(): DeviceAttributeValidationLog
    {
        try {
            $this->calculateSimilarityRatio();
            $this->persistValidationResult($this->bestBrandMatch[1]);
        } catch (Exception $e) {
            $this->persistValidationResult(0);
        } finally {
            return $this->result;
        }
    }

    /**
     * Normalizes the attribute brand by removing accents, digits and special characters.
     */
    private function normalizeBrand(string $brand): string
    {
        return $this->extractOnlyLetters($brand);
    }

    /**
     * Normalizes the attribute invoiceProducts by removing accents, digits and special characters.
     */
    private function normalizeInvoiceProductDescriptions(string $products): string
    {
        return $this->extractOnlyLetters($products);
    }

    /**
     * Calculate ratio score between deviceBrand and invoiceProductDescriptions.
     */
    private function calculateSimilarityRatio(): void
    {
        $wordsOfDescriptions = explode(' ', $this->invoiceProductDescriptions);

        $this->bestBrandMatch = $this->process->extract(
            $this->deviceBrand, $wordsOfDescriptions, null, [$this->fuzz, 'ratio']
        )[0];
    }

    /**
     * Persists the validation results in the database.
     */
    private function persistValidationResult($similarityRatio): void
    {
        $validated = $similarityRatio >= self::MIN_BRAND_SIMILARITY;

        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => get_class($this->device),
            'attribute_label' => 'brand.name',
            'attribute_value' => $this->deviceBrand,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProductDescriptions,
            'invoice_validated_value' => $validated ? $this->bestBrandMatch[0] : null,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => self::MIN_BRAND_SIMILARITY,
            'validated' => $validated,
        ]);
    }
}
