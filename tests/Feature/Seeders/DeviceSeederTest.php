<?php

namespace Tests\Feature\Seeders;

use App\Enums\Device\DeviceValidationStatus;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\User;

use Database\Seeders\BrandSeeder;
use Database\Seeders\DeviceModelSeeder;
use Database\Seeders\DeviceSeeder;
use Database\Seeders\UserSeeder;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DeviceSeederTest extends TestCase
{
    use RefreshDatabase;

    private array $data;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->seed([
            BrandSeeder::class,
            DeviceModelSeeder::class,
            UserSeeder::class,
            DeviceSeeder::class
        ]);

        $json = File::get(database_path('data/devices.json'));
        $this->data = json_decode($json);
    }

    public function test_must_have_created_the_correct_number_of_device_records(): void
    {
        $this->assertEquals(Device::count(), count($this->data));
    }

    public function test_the_device_records_must_have_been_generated_correctly(): void
    {
        foreach ($this->data as $item) {
            $device = Device::where([
                'imei_1' => $item->imei1
            ])->first();

            $this->assertNotNull($device);

            $this->assertEquals($device->imei_2, $item->imei2);
            $this->assertEquals($device->color, $item->color);
            $this->assertEquals($device->user->name, $item->user->name);

            $this->assertEquals(
                $device->deviceModel->name,
                $item->model->name
            );

            $this->assertEquals(
                $device->validation_status,
                DeviceValidationStatus::VALIDATED
            );

            $this->assertEquals(
                $device->invoices()->first()->access_key,
                $item->invoice->access_key
            );
        }
    }
}
