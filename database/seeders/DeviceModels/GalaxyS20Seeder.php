<?php

namespace Database\Seeders\DeviceModels;

use App\Models\Brand;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;

class GalaxyS20Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * https://www.tudocelular.com/Samsung/fichas-tecnicas/n6124/Samsung-Galaxy-S20.html
     */
    public function run(): void
    {
        $brand = Brand::where(['name' => 'Samsung'])->firstOrFail();

        DeviceModel::updateOrCreate([
            'name' => 'Galaxy S20',
            'chipset' => 'Samsung Exynos 990',
            'ram' => '8 GB',
            'storage' => '128 GB',
            'screen_size' => '6.2',
            'screen_resolution' => '1440 x 3200',
            'battery_capacity' => '4000',
            'year_of_manufacture' => '2020',
            'os' => 'Android',
            'brand_id' => $brand->id
        ]);
    }
}
