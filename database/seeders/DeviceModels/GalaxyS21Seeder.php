<?php

namespace Database\Seeders\DeviceModels;

use App\Models\Brand;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;

class GalaxyS21Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * https://www.tudocelular.com/Samsung/fichas-tecnicas/n6637/Samsung-Galaxy-S21.html
     */
    public function run(): void
    {
        $brand = Brand::where(['name' => 'Samsung'])->firstOrFail();

        DeviceModel::updateOrCreate([
            'name' => 'Galaxy S21',
            'chipset' => 'Samsung Exynos 2100',
            'ram' => '8 GB',
            'storage' => '128 GB',
            'screen_size' => '6.2',
            'screen_resolution' => '1080 x 2400',
            'battery_capacity' => '4000',
            'year_of_manufacture' => '2021',
            'os' => 'Android',
            'brand_id' => $brand->id
        ]);
    }
}
