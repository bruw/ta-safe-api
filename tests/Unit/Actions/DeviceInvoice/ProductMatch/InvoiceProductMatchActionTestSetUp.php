<?php

namespace Tests\Unit\Actions\DeviceInvoice\ProductMatch;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceProductMatchActionTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Brand $brand;
    protected Device $device;
    protected DeviceModel $deviceModel;
    protected Invoice $invoice;

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
            ->create(['color' => 'preto']);
    }

    protected function deviceDescription(): string
    {
        return "Smartphone {$this->deviceModel->brand->name}"
            . " {$this->deviceModel->name}"
            . " {$this->deviceModel->ram}"
            . " {$this->deviceModel->storage}"
            . " {$this->device->color}";
    }

    protected function ambiguousInvoiceDescription(): string
    {
        $cover = "<span> Capa para smartphone {$this->deviceModel->brand->name}"
                . " {$this->deviceModel->name}"
                . " {$this->device->color}"
                . '</span>';

        $charger = "<span> Carregador {$this->deviceModel->brand->name}"
                . " {$this->deviceModel->name}"
                . " {$this->device->color}"
                . ' 20w'
                . '</span>';

        return $cover . $charger;
    }
}
