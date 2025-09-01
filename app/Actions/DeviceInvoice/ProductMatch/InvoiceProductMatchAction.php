<?php

namespace App\Actions\DeviceInvoice\ProductMatch;

use App\Dto\Invoice\Search\InvoiceProductMatchResultDto;
use App\Models\Device;
use App\Utils\StringNormalize;
use FuzzyWuzzy\Fuzz;

class InvoiceProductMatchAction
{
    private Fuzz $fuzz;
    private readonly array $attributes;

    public function __construct(
        private readonly Device $device
    ) {
        $this->fuzz = new Fuzz;
        $this->attributes = $this->assignAttributeWeights();
    }

    /**
     * Searches for the product with the greatest similarity to the device
     * in the invoice description.
     */
    public function execute(): InvoiceProductMatchResultDto
    {
        $products = $this->extractProductLines();
        $result = $this->findMatchingProduct($products);

        return new InvoiceProductMatchResultDto(
            product: $result['product'],
            similarityScore: $result['similarity_score']
        );
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
    private function findMatchingProduct(array $products): array
    {
        $matching = ['product' => '', 'similarity_score' => 0];

        foreach ($products as $product) {
            $similarityScore = $this->calculateCumulativeSimilarity($product);

            if ($similarityScore > $matching['similarity_score']) {
                $product = StringNormalize::for($product)->removeExtraWhiteSpaces()->get();

                $matching['product'] = $product;
                $matching['similarity_score'] = $similarityScore;
            }
        }

        return $matching;
    }

    /**
     * Calculates the attribute similarity of device attributes to product items.
     */
    private function calculateCumulativeSimilarity(string $product): int
    {
        $totalSimilarityScore = 0;
        $normalizedProduct = $this->normalize($product);

        foreach ($this->attributes as $attribute) {
            $normalizedValue = $this->normalize($attribute['value']);
            $similarityScore = $this->fuzz->tokenSetRatio($normalizedValue, $normalizedProduct);
            $totalSimilarityScore += $similarityScore * $attribute['weight'];
        }

        return $totalSimilarityScore;
    }

    /**
     * Normalizes the given string value.
     */
    private function normalize(string $value): string
    {
        return StringNormalize::for($value)
            ->removeAccents()
            ->removeExtraWhiteSpaces()
            ->normalizeMemorySize()
            ->toLowerCase()
            ->get();
    }
}
