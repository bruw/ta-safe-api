<?php

namespace Tests\Feature\Factory;

use App\Models\Brand;
use App\Models\DeviceModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceModelFactoryTest extends TestCase
{
    use RefreshDatabase;

    private Brand $brand;

    protected function setUp(): void
    {
        parent::SetUp();
        $this->brand = Brand::factory()->create();
    }

    public function test_must_correctly_instantiate_a_device_model_without_persisting_in_the_database(): void
    {
        $deviceModel = DeviceModel::factory()
            ->for($this->brand)
            ->make();

        $this->assertInstanceOf(DeviceModel::class, $deviceModel);
        $this->assertModelMissing($deviceModel);

        $this->assertNotNull($deviceModel->name);
        $this->assertNotNull($deviceModel->ram);
        $this->assertNotNull($deviceModel->storage);
    }

    public function test_must_correctly_instantiate_a_device_model_and_persist_in_the_database(): void
    {
        $deviceModel = DeviceModel::factory()
            ->for($this->brand)
            ->create();

        $this->assertInstanceOf(DeviceModel::class, $deviceModel);
        $this->assertModelExists($deviceModel);

        $this->assertNotNull($deviceModel->name);
        $this->assertNotNull($deviceModel->ram);
        $this->assertNotNull($deviceModel->storage);
    }
}
