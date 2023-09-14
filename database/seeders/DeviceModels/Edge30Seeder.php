<?php

namespace Database\Seeders\DeviceModels;

use App\Models\Brand;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;

class Edge30Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * https://www.tudocelular.com/Motorola/fichas-tecnicas/n7824/Motorola-Edge-30.html
     */
    public function run(): void
    {
        $brand = Brand::where(['name' => 'Motorola'])->firstOrFail();

        DeviceModel::updateOrCreate([
            'name' => 'Edge 30',
            'chipset' => 'Snapdragon 778G Plus Qualcomm SM7325-AE',
            'ram' => '8 GB',
            'storage' => '256 GB',
            'screen_size' => '6.5',
            'screen_resolution' => '1080 x 2400',
            'battery_capacity' => '4020',
            'year_of_manufacture' => '2022',
            'os' => 'Android',
            'brand_id' => $brand->id
        ]);
    }
}
