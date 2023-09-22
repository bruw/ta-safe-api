<?php

namespace Tests\Feature\Factory;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceFactoryTest extends TestCase
{
    use RefreshDatabase;

    private DeviceModel $deviceModel;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();
    }

    public function test_must_correctly_instantiate_a_device_without_persisting_in_the_database(): void
    {
        $device = Device::factory()->make();

        $this->assertInstanceOf(Device::class, $device);
        $this->assertModelMissing($device);

        $this->assertNotNull($device->color);
        $this->assertNotNull($device->imei_1);
        $this->assertNotNull($device->imei_2);
    }

    public function test_must_correctly_instantiate_a_device_and_persist_in_the_database(): void
    {
        $device = Device::factory()
            ->for(User::factory())
            ->for($this->deviceModel)
            ->create();

        $this->assertInstanceOf(Device::class, $device);
        $this->assertModelExists($device);

        $this->assertNotNull($device->color);
        $this->assertNotNull($device->imei_1);
        $this->assertNotNull($device->imei_2);
    }
}
