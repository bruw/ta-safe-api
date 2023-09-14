<?php

namespace Database\Seeders\DeviceModels;

use App\Models\Brand;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;

class PocoX4ProSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * https://www.tudocelular.com/Poco/fichas-tecnicas/n7654/Poco-X4-Pro.html
     */
    public function run(): void
    {
        $brand = Brand::where(['name' => 'Xiaomi'])->firstOrFail();

        DeviceModel::updateOrCreate([
            'name' => 'Poco X4 Pro',
            'chipset' => 'Snapdragon 695 Qualcomm SM6375',
            'ram' => '6 GB',
            'storage' => '128 GB',
            'screen_size' => '6.67',
            'screen_resolution' => '1080 x 2400',
            'battery_capacity' => '5000',
            'year_of_manufacture' => '2022',
            'os' => 'Android',
            'brand_id' => $brand->id
        ]);;
    }
}
