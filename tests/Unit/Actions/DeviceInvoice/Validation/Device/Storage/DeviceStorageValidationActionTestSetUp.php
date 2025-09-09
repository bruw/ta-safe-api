<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\Device\Storage;

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

class DeviceStorageValidationActionTestSetUp extends TestCase
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
        $this->brand = BrandFactory::new()->create(['name' => 'Apple']);
    }

    private function deviceModelSetUp(): void
    {
        $this->deviceModel = DeviceModelFactory::new()
            ->for($this->brand)
            ->create([
                'name' => 'iPhone 11',
                'ram' => '64 gb',
                'storage' => '4 gb',
            ]);
    }

    private function deviceSetUp(): void
    {
        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->for($this->deviceModel)
            ->inAnalysis()
            ->create(['color' => 'Preto']);
    }

    protected function invoiceProduct(?string $storage = null): string
    {
        $storage ??= $this->deviceModel->storage;

        return "Smartphone {$this->brand->name}"
            . " {$this->deviceModel->name}"
            . " {$this->deviceModel->ram}"
            . " {$storage}"
            . " {$this->device->color}";
    }
}
