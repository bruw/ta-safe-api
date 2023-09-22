<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\DeviceModel;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DeviceModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/device-models.json'));
        $data = json_decode($json);

        foreach ($data as $item) {
            $brand = Brand::where(['name' => $item->brand])->firstOrFail();

            DeviceModel::updateOrCreate([
                'name' => $item->name,
                'ram' => $item->ram,
                'storage' => $item->storage,
                'brand_id' => $brand->id
            ]);
        }
    }
}
