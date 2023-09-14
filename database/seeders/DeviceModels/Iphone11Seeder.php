<?php

namespace Database\Seeders\DeviceModels;

use App\Models\Brand;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;

class Iphone11Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brand = Brand::where(['name' => 'Apple'])->firstOrFail();

        $this->seedIphone64Gb($brand);
        $this->seedIphone128Gb($brand);
        $this->seedIphone256Gb($brand);
    }

    /**
     * Run seed for iphone 11 - 64 gb.
     * https://support.apple.com/kb/SP804?locale=pt_BR
     * 
     * @param \App\Models\Brand $brand
     * @return void
     */
    private function seedIphone64Gb(Brand $brand): void
    {
        DeviceModel::updateOrCreate([
            'name' => 'Iphone 11',
            'chipset' => 'Apple A13 Bionic',
            'ram' => '4 GB',
            'storage' => '64 GB',
            'screen_size' => '6.1',
            'screen_resolution' => '828 x 1792',
            'battery_capacity' => '3110',
            'year_of_manufacture' => '2019',
            'os' => 'IOS',
            'brand_id' => $brand->id
        ]);
    }

    /**
     * Run seed for iphone 11 - 128 gb.
     * https://support.apple.com/kb/SP804?locale=pt_BR
     * 
     * @param \App\Models\Brand $brand
     * @return void
     */
    private function seedIphone128Gb(Brand $brand): void
    {
        DeviceModel::updateOrCreate([
            'name' => 'Iphone 11',
            'chipset' => 'Apple A13 Bionic',
            'ram' => '4 GB',
            'storage' => '128 GB',
            'screen_size' => '6.1',
            'screen_resolution' => '828 x 1792',
            'battery_capacity' => '3110',
            'year_of_manufacture' => '2019',
            'os' => 'IOS',
            'brand_id' => $brand->id
        ]);
    }

    /**
     * Run seed for iphone 11 - 256 gb.
     * https://support.apple.com/kb/SP804?locale=pt_BR
     * 
     * @param \App\Models\Brand $brand
     * @return void
     */
    private function seedIphone256Gb(Brand $brand): void
    {
        DeviceModel::updateOrCreate([
            'name' => 'Iphone 11',
            'chipset' => 'Apple A13 Bionic',
            'ram' => '4 GB',
            'storage' => '256 GB',
            'screen_size' => '6.1',
            'screen_resolution' => '828 x 1792',
            'battery_capacity' => '3110',
            'year_of_manufacture' => '2019',
            'os' => 'IOS',
            'brand_id' => $brand->id
        ]);
    }
}
