<?php

namespace Tests\Unit\Actions\Device\Validate;

use App\Dto\Device\Invoice\DeviceInvoiceDto;
use App\Models\Device;
use App\Models\User;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartDeviceValidationActionTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Device $device;
    protected DeviceInvoiceDto $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->deviceSetUp();
        $this->dataSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

    private function deviceSetUp(): void
    {
        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->create();
    }

    private function dataSetUp(): void
    {
        $products = "{$this->device->deviceModel->brand->name} "
            . " {$this->device->deviceModel->name} "
            . " {$this->device->color} "
            . " {$this->device->deviceModel->storage} "
            . " {$this->device->deviceModel->ram} ";

        $this->data = new DeviceInvoiceDto(
            name: $this->user->name,
            cpf: $this->user->cpf,
            products: $products
        );
    }
}
