<?php

namespace Database\Seeders\DeviceModels;

use App\Models\Brand;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;

class Edge20Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * https://www.tudocelular.com/Motorola/fichas-tecnicas/n7129/Motorola-Edge-20.html
     */
    public function run(): void
    {
        $brand = Brand::where(['name' => 'Motorola'])->firstOrFail();

        DeviceModel::updateOrCreate([
            'name' => 'Edge 20',
            'chipset' => 'Snapdragon 778G Qualcomm SM7325',
            'ram' => '8 GB',
            'storage' => '128 GB',
            'screen_size' => '6.67',
            'screen_resolution' => '1080 x 2400',
            'battery_capacity' => '4000',
            'year_of_manufacture' => '2021',
            'os' => 'Android',
            'brand_id' => $brand->id
        ]);
    }
}
