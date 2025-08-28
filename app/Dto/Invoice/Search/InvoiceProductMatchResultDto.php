<?php

namespace App\Dto\Invoice\Search;

class InvoiceProductMatchResultDto
{
    public function __construct(
        public readonly string $product,
        public readonly int $similarityScore
    ) {}
}
