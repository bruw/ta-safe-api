<?php

namespace Tests\Feature\Seeders;

use App\Models\Brand;
use App\Models\DeviceModel;
use Database\Seeders\BrandSeeder;
use Database\Seeders\DeviceModelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DeviceModelSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->seed([
            BrandSeeder::class,
            DeviceModelSeeder::class
        ]);
    }

    public function test_must_have_created_the_correct_number_of_device_models(): void
    {
        $json = File::get(database_path('data/device-models.json'));
        $data = json_decode($json);

        $this->assertEquals(DeviceModel::count(), count($data));
    }

    public function test_the_device_models_attributes_must_have_been_generated_correctly(): void
    {
        $json = File::get(database_path('data/device-models.json'));
        $data = json_decode($json);

        foreach ($data as $item) {
            $deviceModel = DeviceModel::where([
                'name' => $item->name,
                'ram' => $item->ram,
                'storage' => $item->storage
            ])->first();

            $brand = Brand::where(['name' => $item->brand])->first();

            $this->assertNotNull($deviceModel);

            $this->assertEquals($deviceModel->name, $item->name);
            $this->assertEquals($deviceModel->ram, $item->ram);
            $this->assertEquals($deviceModel->storage, $item->storage);
            $this->assertEquals($deviceModel->brand->id, $brand->id);
        }
    }
}
