<?php

namespace App\Actions\DeviceInvoiceProductValidation;

use App\Models\Device;
use App\Traits\StringNormalizer;
use FuzzyWuzzy\Fuzz;

class FindProductInInvoiceMatchingDeviceAction
{
    use StringNormalizer;

    private Fuzz $fuzz;
    private const MIN_SIMILARITY_SCORE = 795;

    public function __construct(private Device $device)
    {
        $this->fuzz = new Fuzz();
    }

    public function execute(): string
    {
        $invoiceProducts = $this->extractProductLines();

        if (! empty($invoiceProducts)) {
            return $this->findMatchingProduct($invoiceProducts);
        }

        return '';
    }

    /**
     * Extract individual product lines from the invoice description.
     * Uses span tag to separate the beginning and end of the description.
     */
    private function extractProductLines(): array
    {
        $invoiceDescription = $this->device->invoice->product_description;
        preg_match_all('/<span>(.*?)<\/span>/', $invoiceDescription, $matches);

        return $matches[1];
    }

    /**
     * Search for the product with the greatest similarity to the device.
     */
    private function findMatchingProduct(array $invoiceProducts): string
    {
        $bestMatchingProduct = '';
        $bestMatchingScore = 0;

        foreach ($invoiceProducts as $product) {
            $similarity = $this->calculateCumulativeSimilarity($product);

            if ($similarity > $bestMatchingScore) {
                $bestMatchingProduct = $product;
                $bestMatchingScore = $similarity;
            }
        }

        if ($bestMatchingScore >= $this::MIN_SIMILARITY_SCORE) {
            return $bestMatchingProduct;
        }

        return '';
    }

    /**
     * Calculates the attribute similarity of device attributes to product items.
     */
    private function calculateCumulativeSimilarity(string $product): int
    {
        $totalSimilarity = 0;
        $deviceAttributes = $this->assignAttributeWeights();

        foreach ($deviceAttributes as $attribute) {
            $attributeValue = $this->normalizeDescription($attribute['value']);
            $product = $this->normalizeDescription($product);

            $attributeSimilarity = $this->fuzz->tokenSetRatio($attributeValue, $product);

            $totalSimilarity += $attributeSimilarity * $attribute['weight'];
        }

        return $totalSimilarity;
    }

    /**
     * Assign weights to device attributes for similarity calculation.
     */
    private function assignAttributeWeights(): array
    {
        return [
            ['value' => $this->device->deviceModel->brand->name, 'weight' => 1],
            ['value' => $this->device->deviceModel->name, 'weight' => 6],
            ['value' => $this->device->deviceModel->ram, 'weight' => 3],
            ['value' => $this->device->deviceModel->storage, 'weight' => 3],
            ['value' => $this->device->color, 'weight' => 1],
        ];
    }

    /**
     * Normalizes the description by removing accents and whitespaces.
     */
    private function normalizeDescription(string $description): string
    {
        $withoutAccents = $this->removeAccents($description);
        $withoutExtraWhiteSpaces = $this->removeExtraWhiteSpaces($withoutAccents);
        $normalizeMemorys = $this->normalizeMemorySize($withoutExtraWhiteSpaces);
        $result = strtolower($normalizeMemorys);

        return $result;
    }
}
