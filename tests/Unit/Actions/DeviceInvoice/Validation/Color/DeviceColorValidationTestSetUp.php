<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\Color;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\User;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceColorValidationTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Brand $brand;
    protected Device $device;
    protected DeviceModel $deviceModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->brandSetUp();
        $this->deviceModelSetUp();
        $this->deviceSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

    private function brandSetUp(): void
    {
        $this->brand = BrandFactory::new()->create(['name' => 'Samsung']);
    }

    private function deviceModelSetUp(): void
    {
        $this->deviceModel = DeviceModelFactory::new()
            ->for($this->brand)
            ->create([
                'name' => 'Galaxy S23',
                'ram' => '128 gb',
                'storage' => '8 gb',
            ]);
    }

    private function deviceSetUp(): void
    {
        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->for($this->deviceModel)
            ->inAnalysis()
            ->create(['color' => 'Azul']);
    }

    protected function invoiceProduct(?string $brand = null): string
    {
        $brand ??= $this->brand->name;

        return "Smartphone {$brand}"
            . " {$this->deviceModel->name}"
            . " {$this->deviceModel->ram}"
            . " {$this->deviceModel->storage}"
            . " {$this->device->color}";
    }
}
