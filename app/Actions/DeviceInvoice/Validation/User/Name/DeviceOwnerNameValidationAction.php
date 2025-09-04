<?php

namespace App\Actions\DeviceInvoice\Validation\User\Name;

use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Models\User;
use App\Utils\StringNormalize;
use Exception;
use FuzzyWuzzy\Fuzz;

class DeviceOwnerNameValidationAction
{
    private Fuzz $fuzz;
    private DeviceAttributeValidationLog $log;

    public function __construct(
        private readonly Device $device,
    ) {
        $this->fuzz = new Fuzz;
    }

    /**
     * Runs the validation process.
     */
    public function execute(): DeviceAttributeValidationLog
    {
        try {
            $similarityRatio = $this->calculateSimilarityRatio();
            $this->persistValidationResult($similarityRatio);
        } catch (Exception $e) {
            $this->persistValidationResult(similarityRatio: 0);
        } finally {
            return $this->log;
        }
    }

    /**
     * Calculates the similarity ratio between the device owner name and the invoice consumer name.
     */
    private function calculateSimilarityRatio(): int
    {
        return $this->fuzz->ratio(
            $this->normalize($this->device->user->name),
            $this->normalize($this->device->invoice->consumer_name)
        );
    }

    /**
     * Returns the given value normalized.
     */
    private function normalize(string $value): string
    {
        return StringNormalize::for($value)
            ->removeAccents()
            ->keepOnlyLetters()
            ->removeExtraWhiteSpaces()
            ->toLowerCase()
            ->get();
    }

    /**
     * Persists the validation result in the database.
     */
    private function persistValidationResult(int $similarityRatio): void
    {
        $this->log = DeviceAttributeValidationLog::create([
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => User::class,
            'attribute_label' => 'user_name',
            'attribute_value' => $this->device->user->name,
            'invoice_attribute_label' => 'consumer_name',
            'invoice_attribute_value' => $this->device->invoice->consumer_name,
            'similarity_ratio' => $similarityRatio,
            'min_similarity_ratio' => DeviceAttributeValidationRatio::MIN_NAME_SIMILARITY,
            'validated' => $this->validateResult($similarityRatio),
        ]);
    }

    /**
     * Returns whether the given similarity ratio is enough to validate the result.
     */
    private function validateResult(int $similarityRatio): bool
    {
        return $similarityRatio >= DeviceAttributeValidationRatio::MIN_NAME_SIMILARITY;
    }
}
