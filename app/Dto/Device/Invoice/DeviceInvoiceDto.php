<?php

namespace App\Dto\Device\Invoice;

class DeviceInvoiceDto
{
    public function __construct(
        public readonly string $cpf,
        public readonly string $name,
        public readonly string $products
    ) {}
}
