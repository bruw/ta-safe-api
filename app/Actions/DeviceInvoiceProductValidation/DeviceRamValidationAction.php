<?php

namespace App\Actions\DeviceInvoiceProductValidation;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Models\DeviceModel;
use App\Traits\StringNormalizer;
use Exception;
use FuzzyWuzzy\Fuzz;

class DeviceRamValidationAction
{
    use StringNormalizer;

    private Fuzz $fuzz;
    private string $deviceModelRam;
    private DeviceAttributeValidationLog $result;
    private const MIN_RAM_SIMILARITY =
        DeviceAttributeValidationRatio::MIN_RAM_SIMILARITY;

    public function __construct(
        private Device $device,
        private string $invoiceProduct,
    ) {
        $this->fuzz = new Fuzz;

        $this->deviceModelRam = $this->normalizeAttribute(
            $this->device->deviceModel->ram
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
     * Normalizes the attribute by unit gb (gigabyte).
     * Example: 16 GB => 16gb | 4 gb => 4gb.
     */
    private function normalizeAttribute(string $attribute): string
    {
        return $this->normalizeMemorySize($attribute);
    }

    /**
     * Calculate token set ratio score between device ram and invoice product description.
     */
    private function calculateSimilarityRatio(): int
    {
        return $this->fuzz->tokenSetRatio(
            $this->deviceModelRam,
            $this->invoiceProduct
        );
    }

    /**
     * Persists the validation results in the database.
     */
    private function persistValidationResult(int $similarityRatio): void
    {
        $validated = $similarityRatio >= self::MIN_RAM_SIMILARITY;

        $this->result = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => DeviceModel::class,
            'attribute_label' => 'ram',
            'attribute_value' => $this->deviceModelRam,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => self::MIN_RAM_SIMILARITY,
            'validated' => $validated,
        ]);
    }
}
