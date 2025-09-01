<?php

namespace App\Actions\DeviceInvoice\Validation\Base;

use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use Exception;
use FuzzyWuzzy\Fuzz;

abstract class BaseDeviceProductValidationAction
{
    protected Fuzz $fuzz;
    protected DeviceAttributeValidationLog $validationLog;

    public function __construct(
        protected readonly Device $device,
        protected readonly string $invoiceProduct,
    ) {
        $this->fuzz = new Fuzz;
    }

    abstract protected function normalize(string $value): string;
    abstract protected function deviceAttributeToValidate(): string;
    abstract protected function minSimilarityRatio(): int;
    abstract protected function attributeSource(): string;
    abstract protected function attributeLabel(): string;

    /**
     * Run the validation process and return the validation result.
     */
    public function execute(): DeviceAttributeValidationLog
    {
        try {
            $similarityRatio = $this->calculateSimilarityRatio();
            $this->persistSuccessValidation($similarityRatio);

        } catch (Exception $e) {
            $this->persistFailValidation();

        } finally {
            return $this->validationLog;
        }
    }

    /**
     * Calculates the similarity ratio between the device attribute value
     * and the invoice product description.
     */
    private function calculateSimilarityRatio(): int
    {
        return $this->fuzz->tokenSetRatio(
            $this->normalize($this->deviceAttributeToValidate()),
            $this->normalize($this->invoiceProduct)
        );
    }

    /**
     * Persist the validation result to the database if the validation was successful.
     */
    private function persistSuccessValidation(int $similarityRatio): void
    {
        $this->createValidationLog($similarityRatio);
    }

    /**
     * Persist the validation result to the database if the validation failed.
     */
    private function persistFailValidation(): void
    {
        $this->createValidationLog(similarityRatio: 0, fails: true);
    }

    /**
     * Create a new device attribute validation log in the database.
     */
    private function createValidationLog(int $similarityRatio, bool $fails = false): void
    {
        $this->validationLog = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => $this->attributeSource(),
            'attribute_label' => $this->attributeLabel(),
            'attribute_value' => $this->deviceAttributeToValidate(),
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct,
            'similarity_ratio' => $fails ? 0 : $similarityRatio,
            'min_similarity_ratio' => $this->minSimilarityRatio(),
            'validated' => $fails ? false : $this->validateResult($similarityRatio),
        ]);
    }

    /**
     * Determine if the validation was successful based on the similarity ratio.
     */
    private function validateResult(int $similarityRatio): bool
    {
        return $similarityRatio >= $this->minSimilarityRatio();
    }

}
