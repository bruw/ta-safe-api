<?php

namespace Database\Seeders\DeviceModels;

use App\Models\Brand;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;

class GalaxyS22Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * https://www.tudocelular.com/Samsung/fichas-tecnicas/n7452/Samsung-Galaxy-S22.html
     */
    public function run(): void
    {
        $brand = Brand::where(['name' => 'Samsung'])->firstOrFail();

        DeviceModel::updateOrCreate([
            'name' => 'Galaxy S22',
            'chipset' => 'Snapdragon 8 Gen1 Qualcomm SM8450',
            'ram' => '8 GB',
            'storage' => '256 GB',
            'screen_size' => '6.1',
            'screen_resolution' => '1080 x 2340',
            'battery_capacity' => '3700',
            'year_of_manufacture' => '2022',
            'os' => 'Android',
            'brand_id' => $brand->id
        ]);
    }
}
