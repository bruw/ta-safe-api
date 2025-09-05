<?php

namespace Tests\Unit\Services\DeviceInvoice;

use App\Dto\Invoice\Search\InvoiceProductMatchResultDto;
use App\Models\Device;
use App\Models\User;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceInvoiceValidationServiceTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->deviceSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

    private function deviceSetUp(): void
    {
        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->inAnalysis()
            ->create();
    }

    protected function invoiceProduct(): InvoiceProductMatchResultDto
    {
        $product = "Smartphone {$this->device->deviceModel->brand->name}"
           . " {$this->device->deviceModel->name}"
           . " {$this->device->deviceModel->ram}"
           . " {$this->device->deviceModel->storage}"
           . " {$this->device->color}";

        return new InvoiceProductMatchResultDto(
            product: $product,
            similarityScore: 1400
        );
    }
}
